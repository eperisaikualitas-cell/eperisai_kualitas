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

    public function index()
    {
        $soal = SoalPerisai::all()->groupBy('kategori');
        $hasil = session('hasil_penilaian');
        return view('perisai.index', compact('soal', 'hasil'));
    }

    public function edit($id)
    {
        $soal = SoalPerisai::all()->groupBy('kategori');
        $edit_riwayat = RiwayatPenilaian::findOrFail($id);
        $edit_details = DetailPenilaian::where('riwayat_id', $id)->get()->keyBy('soal_id');
        
        return view('perisai.index', compact('soal', 'edit_riwayat', 'edit_details'));
    }

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
            
            // CEK STATUS DRAFT
            $is_draft = $request->action === 'draft';
            $status_akhir = $is_draft ? 'draft' : 'selesai';

            if ($total_akhir >= 88.00) {
                $predikat = "Kualitas Tertinggi";
                $warna = "#66FF66"; $teks = "#000000"; 
            } elseif ($total_akhir >= 78.00) {
                $predikat = "Kualitas Tinggi";
                $warna = "#32CD32"; $teks = "#000000"; 
            } elseif ($total_akhir >= 54.00) {
                $predikat = "Kualitas Sedang";
                $warna = "#FFFF00"; $teks = "#000000"; 
            } elseif ($total_akhir >= 32.00) {
                $predikat = "Kualitas Rendah";
                $warna = "#FF0000"; $teks = "#FFFFFF"; 
            } else {
                $predikat = "Kualitas Terendah";
                $warna = "#FF0000"; $teks = "#FFFFFF"; 
            }

            $predikat_final = $is_draft ? "DRAFT" : $predikat;

            $data_riwayat = [
                'nama_satker' => $request->nama_satker,
                'jenis_satker' => $request->jenis_satker,
                'total_nilai' => $total_akhir,
                'predikat' => $predikat_final,
                'penandatangan_id' => $request->penandatangan_id,
                'status' => $status_akhir,
            ];

            // JIKA UPDATE DRAFT LAMA
            if ($request->riwayat_id) {
                $riwayat = RiwayatPenilaian::findOrFail($request->riwayat_id);
                $riwayat->update($data_riwayat);
                DetailPenilaian::where('riwayat_id', $riwayat->id)->delete(); 
            } else {
                // JIKA BUAT BARU
                $riwayat = RiwayatPenilaian::create($data_riwayat);
            }

            foreach ($kategori_valid as $kat) {
                if ($request->has("soal_{$kat}")) {
                    $soal_ids = $request->input("soal_{$kat}");
                    foreach ($soal_ids as $index => $soal_id) {
                        DetailPenilaian::create([
                            'riwayat_id' => $riwayat->id,
                            'soal_id' => $soal_id,
                            'jawaban_yt' => $request->input("yt_{$kat}")[$index] ?? 'Ya',
                            'skor' => $request->input("val_{$kat}")[$index] ?? 5,
                            'temuan_ketidaksesuaian' => $request->input("tm_{$kat}")[$index] ?? null,
                            'catatan' => $request->input("ct_{$kat}")[$index] ?? null,
                        ]);
                    }
                }
            }
            DB::commit();

            if ($is_draft) {
                return redirect()->route('perisai.riwayat')->with('success', 'Draft kuesioner berhasil disimpan! Silakan lanjutkan nanti.');
            }

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

    public function riwayat()
    {
        $riwayat = RiwayatPenilaian::orderBy('created_at', 'desc')->paginate(10);
        return view('perisai.riwayat', compact('riwayat'));
    }

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

    public function destroyRiwayat($id)
    {
        try {
            DetailPenilaian::where('riwayat_id', $id)->delete();
            RiwayatPenilaian::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Riwayat audit berhasil dihapus permanen!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}