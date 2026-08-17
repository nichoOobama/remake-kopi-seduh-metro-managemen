<?php

namespace App\Observers;

use App\Models\Product;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;

/**
 * Catat aktivitas admin saat CRUD produk.
 */
class ProductObserver
{
    public function created(Product $product): void
    {
        ActivityLogger::log(Auth::id(), 'tambah_produk', "Menambahkan produk \"{$product->name}\".");
    }

    public function updated(Product $product): void
    {
        ActivityLogger::log(Auth::id(), 'edit_produk', "Mengubah produk \"{$product->name}\".");
    }

    public function deleted(Product $product): void
    {
        ActivityLogger::log(Auth::id(), 'hapus_produk', "Menghapus produk \"{$product->name}\".");
    }
}
