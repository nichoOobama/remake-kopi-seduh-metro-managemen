<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Produk kopi yang dijual karyawan (dikelola admin via CRUD).
 */
#[Fillable(['name', 'description', 'harga_modal', 'harga_jual', 'stok', 'foto'])]
class Product extends Model
{
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}