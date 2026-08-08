<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder akun awal:
 *  1 admin dan 1 karyawan contoh.
 * Password default: password123
 */
class AkunAwalSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin KopiSeduh',
            'email' => 'admin@kopiseduh.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Nicho Karyawan',
            'email' => 'karyawan@kopiseduh.test',
            'password' => 'password123',
            'role' => 'employee',
        ]);
    }
}