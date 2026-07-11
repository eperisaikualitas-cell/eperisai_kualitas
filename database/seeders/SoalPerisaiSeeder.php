<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoalPerisai;

class SoalPerisaiSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Fasilitas' => ["Tersedia Petugas Duta Layanan", "Tersedia Loket CS", "Ruang Layanan Aduan", "Tanda Penunjuk Arah", "Loket WNI", "Loket WNA", "Antrian WNI", "Antrian WNA", "Map Paspor", "Area Tunggu Kelompok Rentan", "Loket Khusus Rentan", "Alat Bantu Berjalan", "Guiding Block", "Toilet Umum", "Toilet Pemohon", "Toilet Khusus Rentan", "Drop Zone Rentan", "Parkir Umum", "Parkir Pemohon", "Parkir Khusus Rentan", "Tempat Ibadah", "Ruang Menyusui", "Ruang Bermain Anak", "Info Jenis Layanan", "Info Persyaratan", "Info Biaya", "Info Alur Proses", "Barcode WBS", "Informasi Perintah Menteri", "Fasilitas Charging", "Area Tunggu", "Ruang BAP", "Jamuan Pemohon", "Survei IKM", "Info Biaya Elek/Man", "Logo Kemenimipas", "Logo Imigrasi", "Kursi Roda", "Kacamata Bantu", "Baju Kemeja/Kain Penutup"],
            'Paspor' => ["Aplikasi M-Paspor", "Layanan Prioritas Walk-In", "Layanan Percepatan", "Layanan Rusak/Hilang Walk-In", "Map Gratis", "Pemeriksaan Berkas (5 Mnt)", "Input & Pindai (5 Mnt)", "Wawancara & Biometrik (15 Mnt)", "Alokasi Nomor (5 Mnt)", "Cek Data DPRI (5 Mnt)", "Cetak & Uji Kualitas (10 Mnt)", "Laminasi (5 Mnt)", "Penyerahan (5 Mnt)", "Waktu Ambil Sesuai", "Arsip Map", "Gunting MRZ Paspor Lama", "Kesesuaian Berkas Baru", "Kesesuaian Berkas Penggantian", "Kesesuaian Berkas Hilang/Rusak", "Kesesuaian Perubahan Data", "Kesesuaian Berkas Anak", "Berkas Anak Ganda", "Penanganan Duplikasi", "Mekanisme BAP SPLP", "Batal Paspor >30 Hari", "BA Gagal Produksi", "BA Gagal Cetak", "Gunting Fisik Gagal Cetak", "Info Batal ke Pemegang", "Tindaklanjut Status Menggantung"],
            'IzinTinggal' => ["Kesesuaian berkas pemberian izin tinggal Kunjungan", "Jangka waktu penyelesaian pemberian izin tinggal kunjungan (3 hari)", "Kesesuaian berkas permohonan perpanjangan Izin Tinggal Kunjungan.", "Jangka waktu penyelesaian perpanjangan izin tinggal kunjungan (3 hari)", "Kesesuaian berkas permohonan perpanjangan Izin Tinggal Terbatas.", "Jangka waktu penyelesaian perpanjangan izin tinggal terbatas (3 hari)", "Kesesuaian berkas perpanjangan Izin Tinggal Terbatas 1 tahun", "Jangka waktu penyelesaian perpanjangan izin tinggal terbatas 1 tahun (3 hari)", "Kesesuaian berkas perpanjangan Izin Tinggal Terbatas 2 tahun dan lebih dari 2 tahun", "Jangka waktu penyelesaian perpanjangan izin tinggal terbatas 2 tahun dan lebih dari 2 tahun (3 hari)", "Kesesuaian berkas pemberian izin tinggal tetap", "Jangka waktu penyelesaian izin tinggal tetap (3 hari)", "Kesesuaian berkas perpanjangan izin tinggal tetap", "Jangka waktu penyelesaian perpanjangan izin tinggal tetap (3 hari)", "Kesesuaian berkas alih status izin tinggal terbatas dan izin tinggal tetap", "Jangka waktu penyelesaian alih status izin tinggal terbatas dan izin tinggal tetap (3 hari)", "Kesesuaian berkas perubahan Status Sipil", "Jangka waktu penyelesaian berkas perubahan Status Sipil", "Kesesuaian berkas exit Permit Only", "Jangka waktu penyelesaian exit permit only (3 hari)", "kesesuaian berkas Termination Stay Permit", "Jangka waktu penyelesaian Termination Stay Permit (3 hari)", "Kesesuaian berkas izin tinggal golden visa", "Jangka waktu penyelesaian izin tinggal golden visa (3 hari)", "Kesesuaian berkas izin tinggal keadaan terpaksa", "Jangka waktu penyelesaian izin tinggal dalam keadaan terpaksa (3 hari)", "Kesesuaian berkas permohonan pemberian Alih Status Izin Tinggal Kunjungan Ke Izin Tinggal Terbatas", "Kesesuaian berkas permohonan pemberian Alih status Izin Tinggal Terbatas ke Izin Tinggal Tetap", "Kesesuaian berkas permohonan pemberian sertifikat bagi Anak Berkewarganegaraan", "Kesesuaian berkas permohonan untuk pemberian kartu fasilitas Keimigrasian Affidavit", "Kesesuaian berkas permohonan pemberian Surat Keterangan Keimigrasian"],
            'TPI' => ["Area Imigrasi Terbatas", "Jalur Diplomatik/Dinas", "Jalur Prioritas", "Autogate/Konter", "Supervisor", "Asst Supervisor", "Petugas Konter", "Laporan Harian", "Tim Pemeriksaan", "Struktur Kerja TPI", "Tim Admin", "Unit Analisis Penumpang", "Tim Pemeriksaan Datang/Berangkat", "Tim Area Imigrasi", "Tim Autogate", "Sistem Perlintasan", "Cekal/Interpol", "Pemeriksaan Konter", "Kelengkapan (1 Mnt)", "Cek Dokjal/Visa (1 Mnt)", "Cek Cekal (2 Mnt)", "Lanjutan VITAS (10 Mnt)", "Putusan Kakan (10 Mnt)", "Foto & Biometrik (1 Mnt)", "Tera Stiker (1 Mnt)", "Penolakan (1 Mnt)", "Penyerahan Dok (1 Mnt)", "Autogate: Siapkan Dok (2 Mnt)", "Autogate: Profiling (2 Mnt)", "Autogate: Pindai Dok (2 Mnt)", "Autogate: Manual (5 Mnt)", "Autogate: Elektronik (2 Mnt)", "Autogate: Ditolak (1 Jam)", "Autogate: Melintas (2 Mnt)", "Biaya Beban Sesuai", "Penolakan Sesuai", "Profiling Manual (Gangguan)", "Rekap Manual (Gangguan)", "Voucher PNBP VOA"]
        ];

        foreach ($data as $kategori => $pertanyaanArr) {
            foreach ($pertanyaanArr as $pertanyaan) {
                SoalPerisai::create([
                    'kategori' => $kategori,
                    'pertanyaan' => $pertanyaan
                ]);
            }
        }
    }
}