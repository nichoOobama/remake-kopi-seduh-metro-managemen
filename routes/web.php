<?php

use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Halaman publik & autentikasi
|--------------------------------------------------------------------------
*/

// Redirect "/" ke halaman sesuai status login
Route::get('/', function () {
    return auth()->check()
        ? (auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('dashboard'))
        : redirect()->route('login');
});

// Auth (hanya untuk tamu)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.proses');
});

// Logout (harus login)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Halaman yang butuh login (admin & employee)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Dashboard: isi otomatis menyesuaikan role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- Alur gerobak karyawan ---
    Route::get('/gerobak/ambil', [CartController::class, 'tampilkanFormAmbil'])->name('gerobak.ambil');
    Route::post('/gerobak/ambil', [CartController::class, 'ambilGerobak'])->name('gerobak.ambil.proses');
    Route::get('/gerobak/retur', [CartController::class, 'tampilkanFormRetur'])->name('gerobak.retur');
    Route::post('/gerobak/retur', [CartController::class, 'returGerobak'])->name('gerobak.retur.proses');

    // --- Komisi / bagi hasil ---
    Route::get('/komisi', [CommissionController::class, 'index'])->name('komisi.index');
    Route::post('/komisi/{komisi}/ambil', [CommissionController::class, 'ambil'])->name('komisi.ambil');
});

/*
|--------------------------------------------------------------------------
| Admin area (wajib role = admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard & monitoring admin
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD produk
    Route::get('/produk', [AdminProductController::class, 'index'])->name('produk.index');
    Route::get('/produk/tambah', [AdminProductController::class, 'create'])->name('produk.create');
    Route::post('/produk', [AdminProductController::class, 'store'])->name('produk.store');
    Route::get('/produk/{produk}/edit', [AdminProductController::class, 'edit'])->name('produk.edit');
    Route::put('/produk/{produk}', [AdminProductController::class, 'update'])->name('produk.update');
    Route::delete('/produk/{produk}', [AdminProductController::class, 'destroy'])->name('produk.destroy');

    // Kelola pengguna (tambah admin/employee, hapus)
    Route::get('/pengguna', [AdminUserController::class, 'index'])->name('users.index');
    Route::post('/pengguna', [AdminUserController::class, 'store'])->name('users.store');
    Route::delete('/pengguna/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
});