<?php

namespace App\Observers;

use App\Models\Cart;
use App\Support\ActivityLogger;

/**
 * Catat aktivitas karyawan saat gerobak dikembalikan.
 * (Pencatatan "ambil gerobak" dilakukan di CartController setelah
 * cart_items dibuat, karena event created terpicu sebelum items ada.)
 */
class CartObserver
{
    public function updated(Cart $cart): void
    {
        // Hanya catat ketika status berubah menjadi returned
        if ($cart->wasChanged('status') && $cart->status === 'returned') {
            $totalSisa = $cart->items->sum('qty_sisa');

            ActivityLogger::log(
                $cart->user_id,
                'retur_gerobak',
                "Mengembalikan gerobak: sisa retur {$totalSisa} bungkus dikembalikan ke stok."
            );
        }
    }
}
