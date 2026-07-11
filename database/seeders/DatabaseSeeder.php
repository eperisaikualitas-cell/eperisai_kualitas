<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Membuat akun khusus Admin Patnal
        User::updateOrCreate(
            ['email' => 'admin.patnal@imigrasi.go.id'],
            [
                'name' => 'Admin Patnal',
                'username' => 'adminpatnal',
                'nama_satker' => 'Kantor Imigrasi Wonosobo', // <-- TAMBAHAN BARU
                'password' => Hash::make('p@ssword123'),
                // 'punya_tpi' => false, // Buka komentar ini jika tabel users mewajibkan kolom punya_tpi
                // 'role' => 'admin', // Buka komentar ini jika tabel users mewajibkan kolom role
            ]
        );

        // 2. Memanggil seeder soal
        $this->call([
            SoalPerisaiSeeder::class,
        ]);
    }
}