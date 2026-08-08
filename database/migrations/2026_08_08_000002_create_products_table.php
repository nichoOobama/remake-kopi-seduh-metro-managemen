<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel produk kopi. harga_modal = harga beli (cost), harga_jual = harga jual.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('harga_modal', 12, 2); // harga beli / cost
            $table->decimal('harga_jual', 12, 2);  // harga jual ke customer
            $table->integer('stok')->unsigned()->default(0);
            $table->string('foto')->nullable();    // path foto (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};