<?php

namespace App\Helpers;

class RoleHelper
{
    /**
     * Get display name for role
     * guru -> Dosen, siswa -> Mahasiswa, admin -> Admin
     */
    public static function getDisplayName(string $role): string
    {
        return match(strtolower($role)) {
            'guru' => 'Dosen',
            'siswa' => 'Mahasiswa',
            'admin' => 'Admin',
            default => ucfirst($role),
        };
    }

    /**
     * Get CSS classes for role badge
     */
    public static function getBadgeClass(string $role): string
    {
        return match(strtolower($role)) {
            'admin' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
            'guru' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
            'siswa' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300',
        };
    }
}
