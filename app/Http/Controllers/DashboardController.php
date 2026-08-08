<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Commission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Halaman dashboard utama, berubah isi sesuai role:
 * - Admin  : statistik + akses CRUD.
 * - Employee: daftar produk, status gerobak, komisi.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ---- Dashboard Admin ----
        if ($user->isAdmin()) {
            $stats = [
                'total_produk' => Product::count(),
                'total_stok' => (int) Product::sum('stok'),
                'total_karyawan' => User::where('role', 'employee')->count(),
                'gerobak_aktif' => Cart::where('status', 'active')->count(),
                'gerobak_dikembalikan' => Cart::where('status', 'returned')->count(),
                'komisi_pending' => Commission::where('status', 'pending')->sum('upah_20persen'),
                'komisi_dibayar' => Commission::where('status', 'paid')->sum('upah_20persen'),
                'total_penjualan' => Commission::sum('total_penjualan'),
            ];

            return view('admin.dashboard', [
                'stats' => $stats,
                'komisiTerbaru' => Commission::with(['user', 'cart'])->latest()->limit(10)->get(),
            ]);
        }

        // ---- Dashboard Employee ----
        $gerobakAktif = Cart::where('user_id', $user->id)
            ->where('status', 'active')
            ->with('items.product')
            ->latest()
            ->first();

        return view('dashboard.index', [
            'gerobakAktif' => $gerobakAktif,
            'produk' => Product::orderBy('name')->get(),
            'komisiTerakhir' => Commission::where('user_id', $user->id)->latest()->first(),
        ]);
    }
}