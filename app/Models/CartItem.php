<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Detail produk di dalam gerobak.
 * Snapshot harga modal & jual saat diambil, sehingga bagi hasil stabil.
 */
#[Fillable(['cart_id', 'product_id', 'qty_ambil', 'qty_sisa', 'qty_terjual', 'harga_modal', 'harga_jual'])]
class CartItem extends Model
{
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Pemasukan kotor item: qty terjual x harga jual.
     */
    public function subtotalPenjualan(): float
    {
        return $this->qty_terjual * $this->harga_jual;
    }

    /**
     * Modal item: qty terjual x harga modal.
     */
    public function subtotalModal(): float
    {
        return $this->qty_terjual * $this->harga_modal;
    }

    /**
     * Keuntungan kotor item.
     */
    public function keuntungan(): float
    {
        return $this->subtotalPenjualan() - $this->subtotalModal();
    }
}