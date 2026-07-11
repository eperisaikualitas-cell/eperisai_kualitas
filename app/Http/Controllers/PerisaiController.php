<?php

namespace App\Http\Controllers;

use App\Models\SoalPerisai;
use App\Models\RiwayatPenilaian;
use App\Models\DetailPenilaian;
use App\Models\TimPenilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\ExportPerisaiTrait;

class PerisaiController extends Controller
{
    use ExportPerisaiTrait;

    // 1. HALAMAN UTAMA (FORM)
    public function index()
    {
        $soal = SoalPerisai::all()->groupBy('kategori');
        $hasil = session('hasil_penilaian');
        return view('perisai.index', compact('soal', 'hasil'));
    }

    // 2. PROSES HITUNG / SIMPAN
    public function store(Request $request)
    {
        $request->validate([
            'nama_satker' => 'required|string|max:255',
            'jenis_satker' => 'required|in:TPI,NON_TPI',
        ]);

        $kategori_valid = ['Fasilitas', 'Paspor', 'IzinTinggal'];
        if ($request->jenis_satker === 'TPI') {
            $kategori_valid[] = 'TPI';
        }

        if ($request->action === 'excel') { return $this->exportExcel($request, $kategori_valid); }
        if ($request->action === 'word') { return $this->exportWord($request, $kategori_valid); }

        DB::beginTransaction();
        try {
            $total_nilai_diperoleh = 0;
            $total_nilai_maksimal = 0;
            $max_raw_value = 5;
            $results_per_cat = [];

            foreach ($kategori_valid as $kat) {
                if ($request->has("val_{$kat}")) {
                    $vals = $request->input("val_{$kat}");
                    $kategori_aktual = array_sum($vals);
                    $kategori_maksimal = count($vals) * $max_raw_value;
                    $results_per_cat[$kat] = ($kategori_maksimal > 0) ? ($kategori_aktual / $kategori_maksimal) * 100 : 0;
                    $total_nilai_diperoleh += $kategori_aktual;
                    $total_nilai_maksimal += $kategori_maksimal;
                } else {
                    $results_per_cat[$kat] = 0;
                }
            }

            $total_akhir = ($total_nilai_maksimal > 0) ? ($total_nilai_diperoleh / $total_nilai_maksimal) * 100 : 0;
            
            // PENYESUAIAN INTERVAL BARU
            if ($total_akhir >= 88.00) {
                $predikat = "Kualitas Tertinggi";
                $warna = "#66FF66"; $teks = "#000000"; // Hijau Muda (A)
            } elseif ($total_akhir >= 78.00) {
                $predikat = "Kualitas Tinggi";
                $warna = "#32CD32"; $teks = "#000000"; // Hijau (B)
            } elseif ($total_akhir >= 54.00) {
                $predikat = "Kualitas Sedang";
                $warna = "#FFFF00"; $teks = "#000000"; // Kuning (C)
            } elseif ($total_akhir >= 32.00) {
                $predikat = "Kualitas Rendah";
                $warna = "#FF0000"; $teks = "#FFFFFF"; // Merah (D)
            } else {
                $predikat = "Kualitas Terendah";
                $warna = "#FF0000"; $teks = "#FFFFFF"; // Merah (E)
            }

            $riwayat = RiwayatPenilaian::create([
                'nama_satker' => $request->nama_satker,
                'jenis_satker' => $request->jenis_satker,
                'total_nilai' => $total_akhir,
                'predikat' => $predikat,
            ]);

            foreach ($kategori_valid as $kat) {
                if ($request->has("soal_{$kat}")) {
                    $soal_ids = $request->input("soal_{$kat}");
                    foreach ($soal_ids as $index => $soal_id) {
                        DetailPenilaian::create([
                            'riwayat_id' => $riwayat->id,
                            'soal_id' => $soal_id,
                            'jawaban_yt' => $request->input("yt_{$kat}")[$index] ?? 'Ya',
                            'skor' => $request->input("val_{$kat}")[$index] ?? 5,
                            'komentar' => $request->input("cm_{$kat}")[$index] ?? null,
                        ]);
                    }
                }
            }
            DB::commit();

            $hasil = [
                'id' => $riwayat->id,
                'satker' => $request->nama_satker,
                'total' => $total_akhir,
                'predikat' => $predikat,
                'warna' => $warna,
                'teks' => $teks,
                'detail' => $results_per_cat
            ];

            return redirect()->route('perisai.index')->with('hasil_penilaian', $hasil);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // 5. HALAMAN RIWAYAT
    public function riwayat()
    {
        $riwayat = RiwayatPenilaian::orderBy('created_at', 'desc')->get();
        return view('perisai.riwayat', compact('riwayat'));
    }

    // 6. HALAMAN KELOLA KUESIONER
    public function kuesioner()
    {
        $soal = SoalPerisai::orderBy('kategori')->get();
        return view('perisai.kuesioner', compact('soal'));
    }

    public function storeKuesioner(Request $request)
    {
        $request->validate(['kategori' => 'required', 'pertanyaan' => 'required']);
        SoalPerisai::create($request->only(['kategori', 'pertanyaan']));
        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function destroyKuesioner($id)
    {
        SoalPerisai::findOrFail($id)->delete();
        return back()->with('success', 'Soal berhasil dihapus!');
    }

    // 9. HALAMAN KELOLA TIM PENILAI
    public function tim()
    {
        $tim = TimPenilai::all();
        return view('perisai.tim', compact('tim'));
    }

    public function storeTim(Request $request)
    {
        $request->validate(['nama' => 'required', 'nip' => 'required', 'jabatan' => 'required']);
        TimPenilai::create($request->only(['nama', 'nip', 'jabatan']));
        return back()->with('success', 'Pegawai berhasil ditambahkan!');
    }

    public function destroyTim($id)
    {
        TimPenilai::findOrFail($id)->delete();
        return back()->with('success', 'Pegawai berhasil dihapus!');
    }

    public function updateTim(Request $request, $id)
    {
        $request->validate(['nama' => 'required', 'nip' => 'required', 'jabatan' => 'required']);
        TimPenilai::findOrFail($id)->update($request->only(['nama', 'nip', 'jabatan']));
        return back()->with('success', 'Data Pegawai berhasil diperbarui!');
    }

    // FUNGSI HAPUS RIWAYAT
    public function destroyRiwayat($id)
    {
        try {
            // Hapus detail penilaiannya dulu biar tidak jadi data yatim (orphan) di database
            DetailPenilaian::where('riwayat_id', $id)->delete();
            
            // Baru hapus riwayat utamanya
            RiwayatPenilaian::findOrFail($id)->delete();

            return redirect()->back()->with('success', 'Riwayat audit berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}