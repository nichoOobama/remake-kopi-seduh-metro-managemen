<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detail produk dalam gerobak.
     * harga_modal & harga_jual disnapshot saat ambil gerobak agar perhitungan
     * bagi hasil tidak berubah walau harga produk diedit admin.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('qty_ambil')->unsigned();   // jumlah diambil karyawan
            $table->integer('qty_sisa')->unsigned()->default(0); // jumlah dikembalikan (retur)
            $table->integer('qty_terjual')->unsigned()->default(0); // qty_ambil - qty_sisa
            $table->decimal('harga_modal', 12, 2); // snapshot cost
            $table->decimal('harga_jual', 12, 2);  // snapshot harga jual
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};