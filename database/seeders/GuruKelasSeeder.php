<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruKelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat 1 User Guru
        // Login: guru@example.com, password: password
        $guru = User::create([
            'name' => 'Guru Contoh',
            'email' => 'guru@example.com',
            'password' => Hash::make('password'),
            'role' => 'guru',
        ]);

        // Membuat 1 Mata Pelajaran
        $mapel = MataPelajaran::create([
            'nama' => 'Pemrograman Berorientasi Objek (PBO)',
            'deskripsi' => 'Mata pelajaran dasar untuk mempelajari konsep PBO menggunakan Java.'
        ]);

        // Membuat 1 Kelas yang diajar oleh Guru di atas
        $kelas = Kelas::create([
            'nama' => 'XI RPL 1',
            'deskripsi' => 'Kelas PBO untuk siswa XI RPL 1',
            'user_id' => $guru->id,
        ]);

        // Menambahkan mata pelajaran ke kelas dengan guru pengampu
        $kelas->mataPelajaran()->attach($mapel->id, ['user_id' => $guru->id]);

        // Membuat 1 User Siswa
        // Login: siswa@example.com, password: password
        $siswa = User::create([
            'name' => 'Siswa Contoh',
            'email' => 'siswa@example.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        // Mendaftarkan siswa ke kelas yang dibuat
        $siswa->kelasYangDiikuti()->attach($kelas->id);

        // Membuat 1 User Admin
        // Login: admin@example.com, password: password
        User::create([
            'name' => 'Admin Contoh',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }
}
