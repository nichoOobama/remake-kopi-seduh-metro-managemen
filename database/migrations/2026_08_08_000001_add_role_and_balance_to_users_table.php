<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambah kolom role (admin/employee) dan saldo penghasilan pada tabel users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // role: 'admin' atau 'employee' (default employee, registrasi publik = employee)
            $table->string('role', 20)->default('employee')->after('password');

            // saldo penghasilan karyawan yang sudah diambil dari komisi
            $table->decimal('balance', 12, 2)->default(0)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'balance']);
        });
    }
};