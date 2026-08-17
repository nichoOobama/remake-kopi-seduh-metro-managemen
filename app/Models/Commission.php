<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Komisi / bagi hasil karyawan per gerobak yang sudah dikembalikan.
 */
#[Fillable([
    'cart_id',
    'user_id',
    'total_penjualan',
    'total_modal',
    'total_untung',
    'upah_20persen',
    'status',
    'paid_at',
])]
class Commission extends Model
{
    protected $casts = [
        'paid_at' => 'datetime',
        'total_penjualan' => 'decimal:2',
        'total_modal' => 'decimal:2',
        'total_untung' => 'decimal:2',
        'upah_20persen' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Rincian bagi hasil per produk (untuk tampilan transparan).
     */
    public function items(): HasMany
    {
        return $this->hasMany(CommissionItem::class);
    }
}
