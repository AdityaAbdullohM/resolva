<?php

namespace App\Imports;

use App\Models\Kelas;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToModel, WithHeadingRow
{
    /**
     * Map each row to a User model. Expected headings:
     * name, email, role, password (optional), nim/nis (optional), nip (optional), kelas (optional)
     * Kelas dapat diberikan sebagai nama kelas atau ID kelas.
     */
    public function model(array $row)
    {
        if (empty($row['email']) || empty($row['name'])) {
            return null;
        }

        // Skip if user with same email exists
        if (User::where('email', $row['email'])->exists()) {
            return null;
        }

        $password = isset($row['password']) && $row['password'] !== '' ? $row['password'] : 'password';
        $nim = $row['nim'] ?? $row['nis'] ?? null;
        $kelasId = null;

        $kelasValue = $row['kelas'] ?? $row['kelas_id'] ?? $row['kelas_name'] ?? null;
        if (!empty($kelasValue)) {
            $kelasValue = trim((string) $kelasValue);
            if (ctype_digit($kelasValue)) {
                $kelas = Kelas::find((int) $kelasValue);
            } else {
                $kelas = Kelas::whereRaw('LOWER(nama) = ?', [mb_strtolower($kelasValue)])->first();
                if (!$kelas) {
                    $kelas = Kelas::where('nama', 'like', $kelasValue)->first();
                }
            }
            if ($kelas) {
                $kelasId = $kelas->id;
            }
        }

        // Normalize role names: dosen -> guru, mahasiswa -> siswa
        $role = $row['role'] ?? 'siswa';
        $role = strtolower($role);
        if ($role === 'dosen') {
            $role = 'guru';
        } elseif ($role === 'mahasiswa') {
            $role = 'siswa';
        }

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($password),
            'role' => $role,
            'nis' => $nim,
            'nip' => $row['nip'] ?? null,
            'kelas_id' => $kelasId,
        ]);
    }
}
