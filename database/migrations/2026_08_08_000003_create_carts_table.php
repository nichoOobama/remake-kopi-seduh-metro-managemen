<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel gerobak: 1 karyawan hanya boleh punya 1 gerobak aktif.
     */
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['active', 'returned'])->default('active');
            $table->timestamp('taken_at')->nullable();    // waktu ambil gerobak
            $table->timestamp('returned_at')->nullable(); // waktu kembalikan gerobak
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};