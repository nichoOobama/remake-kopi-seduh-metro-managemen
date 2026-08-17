<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rincian bagi hasil per produk (snapshot) agar transparan bagi karyawan
     * dan stabil untuk audit walau harga produk diedit / produk dihapus.
     */
    public function up(): void
    {
        Schema::create('commission_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name'); // snapshot nama produk (aman bila produk dihapus)
            $table->integer('qty_ambil')->unsigned();
            $table->integer('qty_sisa')->unsigned()->default(0);
            $table->integer('qty_terjual')->unsigned()->default(0);
            $table->decimal('harga_modal', 12, 2); // snapshot cost
            $table->decimal('harga_jual', 12, 2);  // snapshot harga jual
            $table->decimal('subtotal_penjualan', 12, 2)->default(0); // terjual x harga jual
            $table->decimal('subtotal_modal', 12, 2)->default(0);     // terjual x harga modal
            $table->decimal('keuntungan', 12, 2)->default(0);         // penjualan - modal
            $table->decimal('upah_item', 12, 2)->default(0);          // 20% x keuntungan item
            $table->timestamps();

            $table->index('commission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_items');
    }
};
