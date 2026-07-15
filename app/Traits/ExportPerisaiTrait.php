<?php

namespace App\Traits;

use App\Models\TimPenilai;
use App\Models\RiwayatPenilaian;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;

trait ExportPerisaiTrait
{
    private function terbilang($angka) {
        $angka = abs($angka);
        $baca = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        if ($angka < 12) return " " . $baca[(int)$angka];
        if ($angka < 20) return $this->terbilang($angka - 10) . " Belas";
        if ($angka < 100) return $this->terbilang($angka / 10) . " Puluh" . $this->terbilang($angka % 10);
        if ($angka < 200) return " Seratus" . $this->terbilang($angka - 100);
        if ($angka < 1000) return $this->terbilang($angka / 100) . " Ratus" . $this->terbilang($angka % 100);
        if ($angka < 2000) return " Seribu" . $this->terbilang($angka - 1000);
        if ($angka < 1000000) return $this->terbilang($angka / 1000) . " Ribu" . $this->terbilang($angka % 1000);
        return "";
    }

    private function exportExcel($request, $kategori_valid)
    {
        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        $rekap_data = [];
        $total_diperoleh = 0;
        $total_maksimal = 0;

        foreach ($kategori_valid as $kat) {
            if ($sheetIndex > 0) $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($kat, 0, 31));

            $sheet->setCellValue('A1', 'HASIL AUDIT KUALITAS LAYANAN');
            $sheet->setCellValue('A2', 'SATUAN KERJA: ' . strtoupper($request->nama_satker));
            $sheet->setCellValue('A3', 'KATEGORI: ' . strtoupper($kat));
            $sheet->setCellValue('A5', 'NO');
            $sheet->setCellValue('B5', 'BUTIR PEMERIKSAAN');
            $sheet->setCellValue('C5', 'YA / TIDAK');
            $sheet->setCellValue('D5', 'NILAI KEPATUHAN');
            $sheet->setCellValue('E5', 'TEMUAN KETIDAKSESUAIAN');
            $sheet->setCellValue('F5', 'CATATAN');

            $sheet->getStyle('A5:F5')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A73E8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $row = 6;
            $teks_soal = $request->input("teks_{$kat}") ?? [];
            $yt_values = $request->input("yt_{$kat}") ?? [];
            $val_values = $request->input("val_{$kat}") ?? [];
            $tm_values = $request->input("tm_{$kat}") ?? [];
            $ct_values = $request->input("ct_{$kat}") ?? [];

            $kategori_aktual = 0;
            $kategori_maksimal = count($teks_soal) * 5;

            foreach ($teks_soal as $index => $teks) {
                $raw_val = $val_values[$index] ?? 0;
                $kategori_aktual += $raw_val;
                
                $sheet->setCellValue('A' . $row, $index + 1);
                $sheet->setCellValue('B' . $row, $teks);
                $sheet->setCellValue('C' . $row, $yt_values[$index] ?? '-');
                $sheet->setCellValue('D' . $row, $raw_val);
                $sheet->setCellValue('E' . $row, $tm_values[$index] ?? '');
                $sheet->setCellValue('F' . $row, $ct_values[$index] ?? '');
                
                $sheet->getStyle('C'.$row.':D'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheetIndex++;

            $skor_kategori = ($kategori_maksimal > 0) ? ($kategori_aktual / $kategori_maksimal) * 100 : 0;
            $rekap_data[$kat] = $skor_kategori;
            $total_diperoleh += $kategori_aktual;
            $total_maksimal += $kategori_maksimal;
        }

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('HASIL NILAI REKAP');

        $sheet->setCellValue('A1', 'REKAPITULASI INDEKS KEPATUHAN KUALITAS LAYANAN');
        $sheet->setCellValue('A2', 'SATUAN KERJA: ' . strtoupper($request->nama_satker));
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        
        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'KATEGORI LAYANAN');
        $sheet->setCellValue('C4', 'NILAI HASIL');

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $row = 5;
        $no = 1;
        $daftarKategoriMap = [
            'Fasilitas' => 'FASILITAS PENUNJANG',
            'Paspor' => 'PENERBITAN PASPOR',
            'IzinTinggal' => 'IZIN TINGGAL',
            'TPI' => 'TPI'
        ];

        foreach ($rekap_data as $kat => $nilai) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $daftarKategoriMap[$kat] ?? strtoupper($kat));
            $sheet->setCellValue('C' . $row, number_format($nilai, 2));
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        $total_akhir = ($total_maksimal > 0) ? ($total_diperoleh / $total_maksimal) * 100 : 0;
        
        if ($total_akhir >= 88.00) {
            $predikat = "Kualitas Tertinggi";
            $pred_bg = '66FF66'; $pred_color = '000000';
        } elseif ($total_akhir >= 78.00) {
            $predikat = "Kualitas Tinggi";
            $pred_bg = '32CD32'; $pred_color = '000000';
        } elseif ($total_akhir >= 54.00) {
            $predikat = "Kualitas Sedang";
            $pred_bg = 'FFFF00'; $pred_color = '000000';
        } elseif ($total_akhir >= 32.00) {
            $predikat = "Kualitas Rendah";
            $pred_bg = 'FF0000'; $pred_color = 'FFFFFF';
        } else {
            $predikat = "Kualitas Terendah";
            $pred_bg = 'FF0000'; $pred_color = 'FFFFFF';
        }

        $row++;
        $sheet->setCellValue('B' . $row, 'INDEKS KEPATUHAN:');
        $sheet->setCellValue('C' . $row, number_format($total_akhir, 2));
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $row++;
        $sheet->setCellValue('B' . $row, 'PREDIKAT:');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('C' . $row, $predikat);
        $sheet->getStyle('C' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => $pred_color]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $pred_bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $row += 3;
        $sheet->setCellValue('A' . $row, 'Keterangan :');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Interval Nilai');
        $sheet->setCellValue('B' . $row, 'Kategori');
        $sheet->setCellValue('C' . $row, 'Zona');
        $sheet->setCellValue('D' . $row, 'Opini');
        
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A73E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $zonaData = [
            ['88.00 - 100', 'A', 'Hijau', 'Kualitas Tertinggi', '66FF66', '000000'],
            ['78.00 - 87.99', 'B', 'Hijau', 'Kualitas Tinggi', '32CD32', '000000'],
            ['54.00 - 77.99', 'C', 'Kuning', 'Kualitas Sedang', 'FFFF00', '000000'],
            ['32.00 - 53.99', 'D', 'Merah', 'Kualitas Rendah', 'FF0000', 'FFFFFF'],
            ['0 - 31.99', 'E', 'Merah', 'Kualitas Terendah', 'FF0000', 'FFFFFF'],
        ];
        
        foreach ($zonaData as $zd) {
            $row++;
            $sheet->setCellValue('A' . $row, $zd[0]);
            $sheet->setCellValue('B' . $row, $zd[1]);
            $sheet->setCellValue('C' . $row, $zd[2]);
            $sheet->setCellValue('D' . $row, $zd[3]);
            
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'font' => ['color' => ['rgb' => $zd[5]]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $zd[4]]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ]);
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Penjaminan_Kualitas_' . str_replace(' ', '_', $request->nama_satker) . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function exportWord($request, $kategori_valid)
    {
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection(['marginLeft' => 1134, 'marginRight' => 1134, 'marginTop' => 1134, 'marginBottom' => 1134]);

        $styleTableKop = ['borderBottomSize' => 18, 'borderBottomColor' => '000000', 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER];
        $phpWord->addTableStyle('KopSurat', $styleTableKop);
        $tableKop = $section->addTable('KopSurat');
        $tableKop->addRow();

        $cellLogo = $tableKop->addCell(2000, ['valign' => 'center']);
        $logoPath = public_path('logo-imipas.png');
        if (file_exists($logoPath)) {
            $cellLogo->addImage($logoPath, ['width' => 70, 'height' => 70, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        }

        $cellTeks = $tableKop->addCell(8000, ['valign' => 'center']);
        $cellTeks->addText('KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN REPUBLIK INDONESIA', ['name' => 'Arial', 'size' => 10], ['alignment' => 'center', 'spaceAfter' => 0]);
        $cellTeks->addText('DIREKTORAT JENDERAL IMIGRASI', ['name' => 'Arial', 'bold' => true, 'size' => 12], ['alignment' => 'center', 'spaceAfter' => 0]);
        $cellTeks->addText('Jalan HR. Rasuna Said Kav. X6 No. 8, Jakarta Selatan 12940', ['name' => 'Arial', 'size' => 9], ['alignment' => 'center', 'spaceAfter' => 0]);
        
        $textRun = $cellTeks->addTextRun(['alignment' => 'center', 'spaceAfter' => 0]);
        $textRun->addText('Telepon (021) 5224658 Ext. 2303 & 2318 Faximile (021) 522 5031 Email ', ['name' => 'Arial', 'size' => 9]);
        $textRun->addText('Dit.Patnal@imigrasi.go.id', ['name' => 'Arial', 'size' => 9, 'color' => '0000FF', 'underline' => 'single']);
        
        $section->addTextBreak(1);
        $section->addText('BERITA ACARA VERIFIKASI PENILAIAN TINGKAT KEPATUHAN KUALITAS LAYANAN KEIMIGRASIAN', ['bold' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');

        $hariArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanArr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $hariText = $hariArr[$now->dayOfWeek];
        $tanggalTerbilang = trim($this->terbilang($now->day));
        $bulanText = $bulanArr[$now->month - 1];
        $tahunTerbilang = trim($this->terbilang($now->year));
        $formatAngka = $now->format('d-m-Y');
        $jam = $now->format('H.i');

        $pembuka = "----Pada hari ini, {$hariText} tanggal {$tanggalTerbilang} bulan {$bulanText} Tahun {$tahunTerbilang} ({$formatAngka}), pukul {$jam} WIB telah dilaksanakan Penjaminan Kualitas Layanan Keimigrasian pada " . strtoupper($request->nama_satker) . " dengan hasil sebagai berikut :--------------------------------------------";
        $section->addText($pembuka, [], ['alignment' => 'both']);
        $section->addTextBreak(1);

        $daftarKategoriMap = [
            'Fasilitas' => 'FASILITAS PENUNJANG',
            'Paspor' => 'PENERBITAN PASPOR',
            'IzinTinggal' => 'IZIN TINGGAL',
            'TPI' => 'TPI'
        ];

        // LOGIKA PERHITUNGAN TOTAL NILAI DAN PREDIKAT
        $total_nilai_diperoleh = 0;
        $total_nilai_maksimal = 0;
        $max_raw_value = 5;
        $rekap_data = [];

        $no_kat = 1;
        foreach ($kategori_valid as $kat) {
            $raw_vals = $request->input("val_{$kat}") ?? [];
            $kategori_aktual = array_sum($raw_vals);
            $kategori_maksimal = count($raw_vals) * $max_raw_value;
            
            $skor = $kategori_maksimal > 0 ? ($kategori_aktual / $kategori_maksimal) * 100 : 0;
            $rekap_data[$kat] = $skor;
            
            $nama_layanan = $daftarKategoriMap[$kat];
            $section->addText($no_kat++ . ". " . $nama_layanan . " : " . number_format($skor, 2), [], ['indentation' => ['left' => 360]]);
            
            $total_nilai_diperoleh += $kategori_aktual;
            $total_nilai_maksimal += $kategori_maksimal;
        }

        $total_akhir = ($total_nilai_maksimal > 0) ? ($total_nilai_diperoleh / $total_nilai_maksimal) * 100 : 0;
        
        if ($total_akhir >= 88.00) { $predikat = "Kualitas Tertinggi"; $pred_bg = '66FF66'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 78.00) { $predikat = "Kualitas Tinggi"; $pred_bg = '32CD32'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 54.00) { $predikat = "Kualitas Sedang"; $pred_bg = 'FFFF00'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 32.00) { $predikat = "Kualitas Rendah"; $pred_bg = 'FF0000'; $pred_color = 'FFFFFF'; } 
        else { $predikat = "Kualitas Terendah"; $pred_bg = 'FF0000'; $pred_color = 'FFFFFF'; }

        $section->addTextBreak(1);
        
        // MENAMPILKAN RINGKASAN DI HALAMAN BERITA ACARA
        $tableHasil = $section->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]);
        $tableHasil->addRow();
        $tableHasil->addCell(2500)->addText('Nilai Total Akhir', ['bold' => true]);
        $tableHasil->addCell(500)->addText(':', ['bold' => true]);
        $tableHasil->addCell(5000)->addText(number_format($total_akhir, 2), ['bold' => true]);
        $tableHasil->addRow();
        $tableHasil->addCell(2500)->addText('Predikat', ['bold' => true]);
        $tableHasil->addCell(500)->addText(':', ['bold' => true]);
        $tableHasil->addCell(5000)->addText($predikat, ['bold' => true]);

        $section->addTextBreak(1);
        $penutup = "----Demikian Berita Acara Hasil Verifikasi ini dibuat dengan sebenarnya atas kekuatan Sumpah Jabatan yang kemudian ditutup dan ditandatangani pada hari, tanggal, bulan dan tahun seperti tersebut di atas.-------------------------------------------";
        $section->addText($penutup, [], ['alignment' => 'both']);
        $section->addTextBreak(2);

        $tglFormat = $now->format('d ') . $bulanText . $now->format(' Y');
        
        // --- BAGIAN TANDA TANGAN DINAMIS ---
        $section->addText('......................................................., ' . $tglFormat, [], ['alignment' => 'right']);
        $section->addText('Tim Penjaminan Kualitas', ['bold' => true], ['alignment' => 'right']);
        $section->addTextBreak(3); 

        $id_penandatangan = $request->input('penandatangan_id');
        $pegawai = $id_penandatangan ? TimPenilai::find($id_penandatangan) : null;

        if ($pegawai) {
            $section->addText($pegawai->nama, ['bold' => true, 'underline' => 'single'], ['alignment' => 'right']);
            $section->addText('NIP. ' . $pegawai->nip, [], ['alignment' => 'right']);
        } else {
            $section->addText('...........................................', ['bold' => true], ['alignment' => 'right']);
            $section->addText('NIP. .................................................', [], ['alignment' => 'right']);
        }

        // ==========================================
        // TAMBAHAN REKAPITULASI EXCEL KE WORD
        // ==========================================
        $section->addPageBreak();
        $section->addText('REKAPITULASI INDEKS KEPATUHAN KUALITAS LAYANAN', ['bold' => true, 'size' => 12]);
        $section->addText('SATUAN KERJA: ' . strtoupper($request->nama_satker), ['bold' => true, 'size' => 11]);
        $section->addTextBreak(1);

        $styleTableRekap = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
        $styleFirstRowRekap = ['bgColor' => '28A745'];
        $phpWord->addTableStyle('RekapTable', $styleTableRekap, $styleFirstRowRekap);
        
        $tableRekap = $section->addTable('RekapTable');
        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('NO', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableRekap->addCell(5000)->addText('KATEGORI LAYANAN', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableRekap->addCell(2500)->addText('NILAI HASIL', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);

        $no_rekap = 1;
        foreach ($rekap_data as $kat => $nilai) {
            $tableRekap->addRow();
            $tableRekap->addCell(1000)->addText($no_rekap++, [], ['alignment' => 'center']);
            $tableRekap->addCell(5000)->addText(strtoupper($daftarKategoriMap[$kat] ?? $kat));
            $tableRekap->addCell(2500)->addText(number_format($nilai, 2), [], ['alignment' => 'right']);
        }

        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('');
        $tableRekap->addCell(5000)->addText('INDEKS KEPATUHAN', ['bold' => true]);
        $tableRekap->addCell(2500)->addText(number_format($total_akhir, 2), ['bold' => true], ['alignment' => 'right']);

        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('');
        $tableRekap->addCell(5000)->addText('PREDIKAT:', ['bold' => true]);
        $tableRekap->addCell(2500, ['bgColor' => $pred_bg])->addText($predikat, ['bold' => true, 'color' => $pred_color], ['alignment' => 'center']);

        $section->addTextBreak(1);
        $section->addText('Keterangan :');

        $styleLegendRow = ['bgColor' => '1A73E8'];
        $phpWord->addTableStyle('LegendTable', $styleTableRekap, $styleLegendRow);
        $tableLegend = $section->addTable('LegendTable');
        $tableLegend->addRow();
        $tableLegend->addCell(2500)->addText('Interval Nilai', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(1500)->addText('Kategori', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(1500)->addText('Zona', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(3000)->addText('Opini', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);

        $zonaData = [
            ['88.00 - 100', 'A', 'Hijau', 'Kualitas Tertinggi', '66FF66', '000000'],
            ['78.00 - 87.99', 'B', 'Hijau', 'Kualitas Tinggi', '32CD32', '000000'],
            ['54.00 - 77.99', 'C', 'Kuning', 'Kualitas Sedang', 'FFFF00', '000000'],
            ['32.00 - 53.99', 'D', 'Merah', 'Kualitas Rendah', 'FF0000', 'FFFFFF'],
            ['0 - 31.99', 'E', 'Merah', 'Kualitas Terendah', 'FF0000', 'FFFFFF'],
        ];

        foreach ($zonaData as $zd) {
            $tableLegend->addRow();
            $tableLegend->addCell(2500, ['bgColor' => $zd[4]])->addText($zd[0], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(1500, ['bgColor' => $zd[4]])->addText($zd[1], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(1500, ['bgColor' => $zd[4]])->addText($zd[2], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(3000, ['bgColor' => $zd[4]])->addText($zd[3], ['color' => $zd[5]], ['alignment' => 'center']);
        }

        // ==========================================
        // TAMBAHAN LAMPIRAN RINCIAN TABEL KE WORD
        // ==========================================
        $section->addPageBreak();
        $section->addText('LAMPIRAN RINCIAN PENILAIAN', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText('Satuan Kerja: ' . strtoupper($request->nama_satker), ['bold' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $styleTable = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
        $styleFirstRow = ['bgColor' => '1A73E8'];
        $phpWord->addTableStyle('DetailTable', $styleTable, $styleFirstRow);
        $fontHeader = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10];
        $fontData = ['size' => 10];

        foreach ($kategori_valid as $kat) {
            $section->addText('KATEGORI: ' . strtoupper($daftarKategoriMap[$kat]), ['bold' => true, 'size' => 11]);
            
            $table = $section->addTable('DetailTable');
            $table->addRow();
            $table->addCell(500)->addText('No', $fontHeader, ['alignment' => 'center']);
            $table->addCell(3500)->addText('Butir Pemeriksaan', $fontHeader, ['alignment' => 'center']);
            $table->addCell(1000)->addText('Ya/Tidak', $fontHeader, ['alignment' => 'center']);
            $table->addCell(1000)->addText('Skor', $fontHeader, ['alignment' => 'center']);
            $table->addCell(2000)->addText('Temuan', $fontHeader, ['alignment' => 'center']);
            $table->addCell(2000)->addText('Catatan', $fontHeader, ['alignment' => 'center']);

            $teks_soal = $request->input("teks_{$kat}") ?? [];
            $yt_values = $request->input("yt_{$kat}") ?? [];
            $val_values = $request->input("val_{$kat}") ?? [];
            $tm_values = $request->input("tm_{$kat}") ?? [];
            $ct_values = $request->input("ct_{$kat}") ?? [];

            foreach ($teks_soal as $index => $teks) {
                $table->addRow();
                $table->addCell(500)->addText($index + 1, $fontData, ['alignment' => 'center']);
                $table->addCell(3500)->addText($teks, $fontData);
                $table->addCell(1000)->addText($yt_values[$index] ?? '-', $fontData, ['alignment' => 'center']);
                $table->addCell(1000)->addText($val_values[$index] ?? '0', $fontData, ['alignment' => 'center']);
                $table->addCell(2000)->addText($tm_values[$index] ?? '-', $fontData);
                $table->addCell(2000)->addText($ct_values[$index] ?? '-', $fontData);
            }
            $section->addTextBreak(1);
        }

        $fileName = 'Penjaminan_Kualitas_' . str_replace(' ', '_', $request->nama_satker) . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter->save($tempFile);
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }

    public function exportExcelRiwayat($id)
    {
        $riwayat = RiwayatPenilaian::findOrFail($id);
        $details = DB::table('detail_penilaian')
            ->join('soal_perisai', 'detail_penilaian.soal_id', '=', 'soal_perisai.id')
            ->where('detail_penilaian.riwayat_id', $id)
            ->select('soal_perisai.kategori', 'soal_perisai.pertanyaan', 'detail_penilaian.jawaban_yt', 'detail_penilaian.skor', 'detail_penilaian.temuan_ketidaksesuaian', 'detail_penilaian.catatan')
            ->get()
            ->groupBy('kategori');

        $spreadsheet = new Spreadsheet();
        $sheetIndex = 0;
        $rekap_data = [];

        foreach ($details as $kat => $items) {
            if ($sheetIndex > 0) $spreadsheet->createSheet();
            $spreadsheet->setActiveSheetIndex($sheetIndex);
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle(substr($kat, 0, 31));

            $sheet->setCellValue('A1', 'HASIL AUDIT KUALITAS LAYANAN');
            $sheet->setCellValue('A2', 'SATUAN KERJA: ' . strtoupper($riwayat->nama_satker));
            $sheet->setCellValue('A3', 'KATEGORI: ' . strtoupper($kat));
            $sheet->setCellValue('A5', 'NO');
            $sheet->setCellValue('B5', 'BUTIR PEMERIKSAAN');
            $sheet->setCellValue('C5', 'YA / TIDAK');
            $sheet->setCellValue('D5', 'NILAI KEPATUHAN');
            $sheet->setCellValue('E5', 'TEMUAN KETIDAKSESUAIAN');
            $sheet->setCellValue('F5', 'CATATAN');

            $sheet->getStyle('A5:F5')->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A73E8']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ]);

            $row = 6;
            $no = 1;
            $sum_skor = 0;
            $count = count($items);

            foreach ($items as $item) {
                $sum_skor += $item->skor;

                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item->pertanyaan);
                $sheet->setCellValue('C' . $row, $item->jawaban_yt);
                $sheet->setCellValue('D' . $row, $item->skor);
                $sheet->setCellValue('E' . $row, $item->temuan_ketidaksesuaian);
                $sheet->setCellValue('F' . $row, $item->catatan);
                
                $sheet->getStyle('C'.$row.':D'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $row++;
            }
            $sheet->getColumnDimension('B')->setWidth(50);
            $sheet->getColumnDimension('C')->setWidth(15);
            $sheet->getColumnDimension('D')->setWidth(20);
            $sheet->getColumnDimension('E')->setWidth(30);
            $sheet->getColumnDimension('F')->setWidth(30);
            $sheetIndex++;

            $skor_kategori = ($count > 0) ? ($sum_skor / ($count * 5)) * 100 : 0;
            $rekap_data[$kat] = $skor_kategori;
        }

        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheetIndex);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('HASIL NILAI REKAP');

        $sheet->setCellValue('A1', 'REKAPITULASI INDEKS KEPATUHAN KUALITAS LAYANAN');
        $sheet->setCellValue('A2', 'SATUAN KERJA: ' . strtoupper($riwayat->nama_satker));
        $sheet->getStyle('A1:A2')->getFont()->setBold(true);
        
        $sheet->setCellValue('A4', 'NO');
        $sheet->setCellValue('B4', 'KATEGORI LAYANAN');
        $sheet->setCellValue('C4', 'NILAI HASIL');

        $sheet->getStyle('A4:C4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '28A745']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ]);

        $row = 5;
        $no = 1;
        $daftarKategoriMap = [
            'Fasilitas' => 'FASILITAS PENUNJANG',
            'Paspor' => 'PENERBITAN PASPOR',
            'IzinTinggal' => 'IZIN TINGGAL',
            'TPI' => 'TPI'
        ];

        foreach ($rekap_data as $kat => $nilai) {
            $sheet->setCellValue('A' . $row, $no++);
            $sheet->setCellValue('B' . $row, $daftarKategoriMap[$kat] ?? strtoupper($kat));
            $sheet->setCellValue('C' . $row, number_format($nilai, 2));
            
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $row++;
        }

        $total_akhir = $riwayat->total_nilai;
        
        if ($total_akhir >= 88.00) {
            $pred_bg = '66FF66'; $pred_color = '000000';
        } elseif ($total_akhir >= 78.00) {
            $pred_bg = '32CD32'; $pred_color = '000000';
        } elseif ($total_akhir >= 54.00) {
            $pred_bg = 'FFFF00'; $pred_color = '000000';
        } elseif ($total_akhir >= 32.00) {
            $pred_bg = 'FF0000'; $pred_color = 'FFFFFF';
        } else {
            $pred_bg = 'FF0000'; $pred_color = 'FFFFFF';
        }

        $row++;
        $sheet->setCellValue('B' . $row, 'INDEKS KEPATUHAN:');
        $sheet->setCellValue('C' . $row, number_format($total_akhir, 2));
        $sheet->getStyle('B' . $row . ':C' . $row)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        
        $row++;
        $sheet->setCellValue('B' . $row, 'PREDIKAT:');
        $sheet->getStyle('B' . $row)->getFont()->setBold(true);

        $sheet->setCellValue('C' . $row, $riwayat->predikat);
        $sheet->getStyle('C' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => $pred_color]],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $pred_bg]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'outline' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $row += 3;
        $sheet->setCellValue('A' . $row, 'Keterangan :');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Interval Nilai');
        $sheet->setCellValue('B' . $row, 'Kategori');
        $sheet->setCellValue('C' . $row, 'Zona');
        $sheet->setCellValue('D' . $row, 'Opini');
        
        $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A73E8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
            ]
        ]);

        $zonaData = [
            ['88.00 - 100', 'A', 'Hijau', 'Kualitas Tertinggi', '66FF66', '000000'],
            ['78.00 - 87.99', 'B', 'Hijau', 'Kualitas Tinggi', '32CD32', '000000'],
            ['54.00 - 77.99', 'C', 'Kuning', 'Kualitas Sedang', 'FFFF00', '000000'],
            ['32.00 - 53.99', 'D', 'Merah', 'Kualitas Rendah', 'FF0000', 'FFFFFF'],
            ['0 - 31.99', 'E', 'Merah', 'Kualitas Terendah', 'FF0000', 'FFFFFF'],
        ];
        
        foreach ($zonaData as $zd) {
            $row++;
            $sheet->setCellValue('A' . $row, $zd[0]);
            $sheet->setCellValue('B' . $row, $zd[1]);
            $sheet->setCellValue('C' . $row, $zd[2]);
            $sheet->setCellValue('D' . $row, $zd[3]);
            
            $sheet->getStyle('A' . $row . ':D' . $row)->applyFromArray([
                'font' => ['color' => ['rgb' => $zd[5]]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $zd[4]]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]
                ]
            ]);
        }

        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(25);

        $spreadsheet->setActiveSheetIndex(0);

        $fileName = 'Penjaminan_Kualitas_' . str_replace(' ', '_', $riwayat->nama_satker) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) { $writer->save('php://output'); }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportWordRiwayat($id, $request = null)
    {
        \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
        $riwayat = RiwayatPenilaian::findOrFail($id);
        
        $details = DB::table('detail_penilaian')
            ->join('soal_perisai', 'detail_penilaian.soal_id', '=', 'soal_perisai.id')
            ->where('detail_penilaian.riwayat_id', $id)
            ->select('soal_perisai.kategori', 'soal_perisai.pertanyaan', 'detail_penilaian.jawaban_yt', 'detail_penilaian.skor', 'detail_penilaian.temuan_ketidaksesuaian', 'detail_penilaian.catatan')
            ->get()
            ->groupBy('kategori');

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection(['marginLeft' => 1134, 'marginRight' => 1134, 'marginTop' => 1134, 'marginBottom' => 1134]);

        $styleTableKop = ['borderBottomSize' => 18, 'borderBottomColor' => '000000', 'cellMargin' => 50, 'alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER];
        $phpWord->addTableStyle('KopSurat', $styleTableKop);
        $tableKop = $section->addTable('KopSurat');
        $tableKop->addRow();

        $cellLogo = $tableKop->addCell(2000, ['valign' => 'center']);
        $logoPath = public_path('logo-imipas.png'); 
        if (file_exists($logoPath)) {
            $cellLogo->addImage($logoPath, ['width' => 70, 'height' => 70, 'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]);
        }

        $cellTeks = $tableKop->addCell(8000, ['valign' => 'center']);
        $cellTeks->addText('KEMENTERIAN IMIGRASI DAN PEMASYARAKATAN REPUBLIK INDONESIA', ['name' => 'Arial', 'size' => 10], ['alignment' => 'center', 'spaceAfter' => 0]);
        $cellTeks->addText('DIREKTORAT JENDERAL IMIGRASI', ['name' => 'Arial', 'bold' => true, 'size' => 12], ['alignment' => 'center', 'spaceAfter' => 0]);
        $cellTeks->addText('Jalan HR. Rasuna Said Kav. X6 No. 8, Jakarta Selatan 12940', ['name' => 'Arial', 'size' => 9], ['alignment' => 'center', 'spaceAfter' => 0]);
        
        $textRun = $cellTeks->addTextRun(['alignment' => 'center', 'spaceAfter' => 0]);
        $textRun->addText('Telepon (021) 5224658 Ext. 2303 & 2318 Faximile (021) 522 5031 Email ', ['name' => 'Arial', 'size' => 9]);
        $textRun->addText('Dit.Patnal@imigrasi.go.id', ['name' => 'Arial', 'size' => 9, 'color' => '0000FF', 'underline' => 'single']);
        
        $section->addTextBreak(1);
        $section->addText('BERITA ACARA VERIFIKASI PENILAIAN TINGKAT KEPATUHAN KUALITAS LAYANAN KEIMIGRASIAN', ['bold' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        Carbon::setLocale('id');
        $waktu = Carbon::parse($riwayat->created_at)->timezone('Asia/Jakarta');

        $hariArr = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $bulanArr = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        
        $hariText = $hariArr[$waktu->dayOfWeek];
        $tanggalTerbilang = trim($this->terbilang($waktu->day));
        $bulanText = $bulanArr[$waktu->month - 1];
        $tahunTerbilang = trim($this->terbilang($waktu->year));
        $formatAngka = $waktu->format('d-m-Y');
        $jam = $waktu->format('H.i');

        $pembuka = "----Pada hari ini, {$hariText} tanggal {$tanggalTerbilang} bulan {$bulanText} Tahun {$tahunTerbilang} ({$formatAngka}), pukul {$jam} WIB telah dilaksanakan Penjaminan Kualitas Layanan Keimigrasian pada " . strtoupper($riwayat->nama_satker) . " dengan hasil sebagai berikut :--------------------------------------------";
        $section->addText($pembuka, [], ['alignment' => 'both']);
        $section->addTextBreak(1);

        $daftarKategoriMap = [
            'Fasilitas' => 'FASILITAS PENUNJANG',
            'Paspor' => 'PENERBITAN PASPOR',
            'IzinTinggal' => 'IZIN TINGGAL',
            'TPI' => 'TPI'
        ];

        $rekap_data = [];
        $no_kat = 1;
        foreach ($details as $kat => $items) {
            $sum_skor = $items->sum('skor');
            $count = $items->count();
            $skor = $count > 0 ? ($sum_skor / ($count * 5)) * 100 : 0;
            $rekap_data[$kat] = $skor;
            
            $nama_layanan = $daftarKategoriMap[$kat] ?? $kat;
            $section->addText($no_kat++ . ". " . $nama_layanan . " : " . number_format($skor, 2), [], ['indentation' => ['left' => 360]]);
        }

        $total_akhir = $riwayat->total_nilai;
        if ($total_akhir >= 88.00) { $predikat = "Kualitas Tertinggi"; $pred_bg = '66FF66'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 78.00) { $predikat = "Kualitas Tinggi"; $pred_bg = '32CD32'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 54.00) { $predikat = "Kualitas Sedang"; $pred_bg = 'FFFF00'; $pred_color = '000000'; } 
        elseif ($total_akhir >= 32.00) { $predikat = "Kualitas Rendah"; $pred_bg = 'FF0000'; $pred_color = 'FFFFFF'; } 
        else { $predikat = "Kualitas Terendah"; $pred_bg = 'FF0000'; $pred_color = 'FFFFFF'; }

        $section->addTextBreak(1);

        // MENAMPILKAN RINGKASAN DI HALAMAN BERITA ACARA
        $tableHasil = $section->addTable(['alignment' => \PhpOffice\PhpWord\SimpleType\JcTable::CENTER]);
        $tableHasil->addRow();
        $tableHasil->addCell(2500)->addText('Nilai Total Akhir', ['bold' => true]);
        $tableHasil->addCell(500)->addText(':', ['bold' => true]);
        $tableHasil->addCell(5000)->addText(number_format($riwayat->total_nilai, 2), ['bold' => true]);
        $tableHasil->addRow();
        $tableHasil->addCell(2500)->addText('Predikat', ['bold' => true]);
        $tableHasil->addCell(500)->addText(':', ['bold' => true]);
        $tableHasil->addCell(5000)->addText($riwayat->predikat, ['bold' => true]);

        $section->addTextBreak(1);
        $penutup = "----Demikian Berita Acara Hasil Verifikasi ini dibuat dengan sebenarnya atas kekuatan Sumpah Jabatan yang kemudian ditutup dan ditandatangani pada hari, tanggal, bulan dan tahun seperti tersebut di atas.-------------------------------------------";
        $section->addText($penutup, [], ['alignment' => 'both']);
        $section->addTextBreak(2);

        $tglFormat = $waktu->format('d ') . $bulanText . $waktu->format(' Y');
        
        // --- BAGIAN TANDA TANGAN DINAMIS ---
        $section->addText('..........................., ' . $tglFormat, [], ['alignment' => 'right']);
        $section->addText('Tim Penjaminan Kualitas', ['bold' => true], ['alignment' => 'right']);
        $section->addTextBreak(3); 

        $pegawai = $riwayat->penandatangan_id ? TimPenilai::find($riwayat->penandatangan_id) : null;

        if ($pegawai) {
            $section->addText($pegawai->nama, ['bold' => true, 'underline' => 'single'], ['alignment' => 'right']);
            $section->addText('NIP. ' . $pegawai->nip, [], ['alignment' => 'right']);
        } else {
            $section->addText('............................................', ['bold' => true], ['alignment' => 'right']);
            $section->addText('NIP. ............................................', [], ['alignment' => 'right']);
        }

        // ==========================================
        // TAMBAHAN REKAPITULASI EXCEL KE WORD
        // ==========================================
        $section->addPageBreak();
        $section->addText('REKAPITULASI INDEKS KEPATUHAN KUALITAS LAYANAN', ['bold' => true, 'size' => 12]);
        $section->addText('SATUAN KERJA: ' . strtoupper($riwayat->nama_satker), ['bold' => true, 'size' => 11]);
        $section->addTextBreak(1);

        $styleTableRekap = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
        $styleFirstRowRekap = ['bgColor' => '28A745'];
        $phpWord->addTableStyle('RekapTable', $styleTableRekap, $styleFirstRowRekap);
        
        $tableRekap = $section->addTable('RekapTable');
        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('NO', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableRekap->addCell(5000)->addText('KATEGORI LAYANAN', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableRekap->addCell(2500)->addText('NILAI HASIL', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);

        $no_rekap = 1;
        foreach ($rekap_data as $kat => $nilai) {
            $tableRekap->addRow();
            $tableRekap->addCell(1000)->addText($no_rekap++, [], ['alignment' => 'center']);
            $tableRekap->addCell(5000)->addText(strtoupper($daftarKategoriMap[$kat] ?? $kat));
            $tableRekap->addCell(2500)->addText(number_format($nilai, 2), [], ['alignment' => 'right']);
        }

        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('');
        $tableRekap->addCell(5000)->addText('INDEKS KEPATUHAN', ['bold' => true]);
        $tableRekap->addCell(2500)->addText(number_format($total_akhir, 2), ['bold' => true], ['alignment' => 'right']);

        $tableRekap->addRow();
        $tableRekap->addCell(1000)->addText('');
        $tableRekap->addCell(5000)->addText('PREDIKAT:', ['bold' => true]);
        $tableRekap->addCell(2500, ['bgColor' => $pred_bg])->addText($predikat, ['bold' => true, 'color' => $pred_color], ['alignment' => 'center']);

        $section->addTextBreak(1);
        $section->addText('Keterangan :');

        $styleLegendRow = ['bgColor' => '1A73E8'];
        $phpWord->addTableStyle('LegendTable', $styleTableRekap, $styleLegendRow);
        $tableLegend = $section->addTable('LegendTable');
        $tableLegend->addRow();
        $tableLegend->addCell(2500)->addText('Interval Nilai', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(1500)->addText('Kategori', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(1500)->addText('Zona', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);
        $tableLegend->addCell(3000)->addText('Opini', ['bold' => true, 'color' => 'FFFFFF'], ['alignment' => 'center']);

        $zonaData = [
            ['88.00 - 100', 'A', 'Hijau', 'Kualitas Tertinggi', '66FF66', '000000'],
            ['78.00 - 87.99', 'B', 'Hijau', 'Kualitas Tinggi', '32CD32', '000000'],
            ['54.00 - 77.99', 'C', 'Kuning', 'Kualitas Sedang', 'FFFF00', '000000'],
            ['32.00 - 53.99', 'D', 'Merah', 'Kualitas Rendah', 'FF0000', 'FFFFFF'],
            ['0 - 31.99', 'E', 'Merah', 'Kualitas Terendah', 'FF0000', 'FFFFFF'],
        ];

        foreach ($zonaData as $zd) {
            $tableLegend->addRow();
            $tableLegend->addCell(2500, ['bgColor' => $zd[4]])->addText($zd[0], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(1500, ['bgColor' => $zd[4]])->addText($zd[1], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(1500, ['bgColor' => $zd[4]])->addText($zd[2], ['color' => $zd[5]], ['alignment' => 'center']);
            $tableLegend->addCell(3000, ['bgColor' => $zd[4]])->addText($zd[3], ['color' => $zd[5]], ['alignment' => 'center']);
        }

        // ==========================================
        // TAMBAHAN LAMPIRAN RINCIAN TABEL KE WORD RIWAYAT
        // ==========================================
        $section->addPageBreak();
        $section->addText('LAMPIRAN RINCIAN PENILAIAN', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $section->addText('Satuan Kerja: ' . strtoupper($riwayat->nama_satker), ['bold' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        $styleTable = ['borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80];
        $styleFirstRow = ['bgColor' => '1A73E8'];
        $phpWord->addTableStyle('DetailTable', $styleTable, $styleFirstRow);
        $fontHeader = ['bold' => true, 'color' => 'FFFFFF', 'size' => 10];
        $fontData = ['size' => 10];

        foreach ($details as $kat => $items) {
            $section->addText('KATEGORI: ' . strtoupper($daftarKategoriMap[$kat] ?? $kat), ['bold' => true, 'size' => 11]);
            
            $table = $section->addTable('DetailTable');
            $table->addRow();
            $table->addCell(500)->addText('No', $fontHeader, ['alignment' => 'center']);
            $table->addCell(3500)->addText('Butir Pemeriksaan', $fontHeader, ['alignment' => 'center']);
            $table->addCell(1000)->addText('Ya/Tidak', $fontHeader, ['alignment' => 'center']);
            $table->addCell(1000)->addText('Skor', $fontHeader, ['alignment' => 'center']);
            $table->addCell(2000)->addText('Temuan', $fontHeader, ['alignment' => 'center']);
            $table->addCell(2000)->addText('Catatan', $fontHeader, ['alignment' => 'center']);

            $no = 1;
            foreach ($items as $item) {
                $table->addRow();
                $table->addCell(500)->addText($no++, $fontData, ['alignment' => 'center']);
                $table->addCell(3500)->addText($item->pertanyaan, $fontData);
                $table->addCell(1000)->addText($item->jawaban_yt ?? '-', $fontData, ['alignment' => 'center']);
                $table->addCell(1000)->addText($item->skor ?? '0', $fontData, ['alignment' => 'center']);
                $table->addCell(2000)->addText($item->temuan_ketidaksesuaian ?? '-', $fontData);
                $table->addCell(2000)->addText($item->catatan ?? '-', $fontData);
            }
            $section->addTextBreak(1);
        }

        $fileName = 'Penjaminan_Kualitas_' . str_replace(' ', '_', $riwayat->nama_satker) . '.docx';
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'PHPWord');
        $objWriter->save($tempFile);
        return response()->download($tempFile, $fileName)->deleteFileAfterSend(true);
    }
}