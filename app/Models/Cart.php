<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Gerobak (keranjang penjualan) milik karyawan.
 * status: active (sedang dibawa) / returned (sudah dikembalikan + dihitung bagi hasil).
 */
#[Fillable(['user_id', 'status', 'taken_at', 'returned_at'])]
class Cart extends Model
{
    protected $casts = [
        'taken_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Komisi terkait gerobak ini (search 1:1).
     */
    public function commission(): HasOne
    {
        return $this->hasOne(Commission::class);
    }
}