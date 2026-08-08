<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use \Illuminate\Database\Console\Seeds\WithoutModelEvents;

    /**
     * Seed data awal: akun admin + karyawan contoh, dan produk kopi contoh.
     */
    public function run(): void
    {
        $this->call([
            AkunAwalSeeder::class,
            ProdukSeeder::class,
        ]);
    }
}