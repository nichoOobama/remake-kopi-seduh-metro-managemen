<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Commission;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Halaman dashboard utama, berubah isi sesuai role:
 * - Admin  : statistik + line chart penjualan 7 hari per karyawan.
 * - Employee: daftar produk, status gerobak, komisi.
 */
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ---- Dashboard Admin ----
        if ($user->isAdmin()) {
            $mulai = now()->subDays(6)->startOfDay();
            $sampai = now()->endOfDay();

            // Labels: 7 hari terakhir (hari ini termasuk)
            $labels = [];
            for ($i = 6; $i >= 0; $i--) {
                $labels[] = now()->subDays($i)->format('d M');
            }

            // 1 query agregat: penjualan per karyawan per hari (7 hari terakhir)
            $agregat = Commission::query()
                ->join('carts', 'commissions.cart_id', '=', 'carts.id')
                ->whereBetween('carts.returned_at', [$mulai, $sampai])
                ->selectRaw('commissions.user_id, DATE(carts.returned_at) AS tgl, SUM(commissions.total_penjualan) AS penjualan, SUM(commissions.upah_20persen) AS upah')
                ->groupBy('commissions.user_id', 'tgl')
                ->get()
                ->keyBy(fn ($d) => $d->user_id.'|'.$d->tgl);

            $karyawan = User::where('role', 'employee')->orderBy('name')->get(['id', 'name']);

            $series = [];
            foreach ($karyawan as $k) {
                $data = [];
                for ($i = 6; $i >= 0; $i--) {
                    $tgl = now()->subDays($i)->format('Y-m-d');
                    $data[] = (float) ($agregat->get($k->id.'|'.$tgl)?->penjualan ?? 0);
                }
                $series[] = ['id' => $k->id, 'name' => $k->name, 'data' => $data];
            }

            $stats = [
                'total_produk' => Product::count(),
                'total_stok' => (int) Product::sum('stok'),
                'total_karyawan' => User::where('role', 'employee')->count(),
                'gerobak_aktif' => Cart::where('status', 'active')->count(),
                'gerobak_dikembalikan' => Cart::where('status', 'returned')->count(),
                'komisi_pending' => Commission::where('status', 'pending')->sum('upah_20persen'),
                'komisi_dibayar' => Commission::where('status', 'paid')->sum('upah_20persen'),
                'total_penjualan' => Commission::sum('total_penjualan'),
                'penjualan_7hari' => (float) $agregat->sum('penjualan'),
                'upah_7hari' => (float) $agregat->sum('upah'),
            ];

            return view('admin.dashboard', [
                'stats' => $stats,
                'komisiTerbaru' => Commission::with(['user', 'cart'])->latest()->limit(10)->get(),
                'chartLabels' => $labels,
                'chartSeries' => $series,
                'chartKaryawan' => $karyawan,
                'persenBagiHasil' => (float) config('komisi.persentase_bagi_hasil'),
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
            'komisiTerakhir' => Commission::with('items')->where('user_id', $user->id)->latest()->first(),
        ]);
    }
}
