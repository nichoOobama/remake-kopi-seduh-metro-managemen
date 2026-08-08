<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

/**
 * Seeder produk kopi contoh + stok awal.
 * Pakai firstOrCreate agar bisa dijalankan berulang tanpa duplikasi.
 */
class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        $produk = [
            [
                'name' => 'Kopi Robusta Lampung 200g',
                'description' => 'Kopi robusta premium dari Lampung, sangrai medium, cocok untuk espresso & tubruk.',
                'harga_modal' => 25000,
                'harga_jual' => 40000,
            ],
            [
                'name' => 'Kopi Arabika Gayo 200g',
                'description' => 'Arabika Gayo dengan rasa manis alami dan sedikit floral.',
                'harga_modal' => 35000,
                'harga_jual' => 55000,
            ],
            [
                'name' => 'Kopi Luwak Premium 100g',
                'description' => 'Kopi luwak pilihan dengan proses pengolahan bersih.',
                'harga_modal' => 80000,
                'harga_jual' => 150000,
            ],
            [
                'name' => 'Kopi Susu Gula Aren 25 sachet',
                'description' => 'Kemasan sachet praktis, cocok untuk reseller.',
                'harga_modal' => 40000,
                'harga_jual' => 60000,
            ],
        ];

        foreach ($produk as $p) {
            Product::firstOrCreate(
                ['name' => $p['name']],
                array_merge($p, ['stok' => 100]),
            );
        }
    }
}