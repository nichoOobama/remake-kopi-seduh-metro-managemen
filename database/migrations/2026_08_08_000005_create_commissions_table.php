<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel komisi / bagi hasil per gerobak yang dikembalikan.
     * upah_20persen = 20% dari total_untung, dihitung otomatis oleh sistem.
     * status: pending -> belum diambil, paid -> sudah masuk saldo karyawan.
     */
    public function up(): void
    {
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('total_penjualan', 12, 2)->default(0); // harga_jual x qty_terjual
            $table->decimal('total_modal', 12, 2)->default(0);     // harga_modal x qty_terjual
            $table->decimal('total_untung', 12, 2)->default(0);    // penjualan - modal
            $table->decimal('upah_20persen', 12, 2)->default(0);   // 20% x total_untung
            $table->enum('status', ['pending', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};