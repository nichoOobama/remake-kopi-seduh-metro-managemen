<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rincian bagi hasil per produk pada sebuah komisi (snapshot).
 * Dihitung & disimpan saat gerobak dikembalikan agar transparan.
 */
#[Fillable([
    'commission_id',
    'product_id',
    'product_name',
    'qty_ambil',
    'qty_sisa',
    'qty_terjual',
    'harga_modal',
    'harga_jual',
    'subtotal_penjualan',
    'subtotal_modal',
    'keuntungan',
    'upah_item',
])]
class CommissionItem extends Model
{
    protected $casts = [
        'harga_modal' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'subtotal_penjualan' => 'decimal:2',
        'subtotal_modal' => 'decimal:2',
        'keuntungan' => 'decimal:2',
        'upah_item' => 'decimal:2',
    ];

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
