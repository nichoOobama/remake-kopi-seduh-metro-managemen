<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Commission;
use Illuminate\Http\Request;

/**
 * Monitoring gerobak karyawan (khusus admin, read-only):
 * - tab "Aktif": siapa membawa apa & berapa jumlahnya.
 * - tab "Dikembalikan": siapa mengembalikan apa, sisa retur, & hasil terjual.
 */
class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'active');
        $status = in_array($status, ['active', 'returned'], true) ? $status : 'active';
        $cari = trim((string) $request->query('q'));

        $gerobak = Cart::query()
            ->with(['user', 'items.product'])
            ->where('status', $status)
            ->when($cari !== '', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%")))
            ->latest('taken_at')
            ->paginate(10)
            ->withQueryString();

        return view('admin.carts.index', [
            'gerobak' => $gerobak,
            'status' => $status,
            'cari' => $cari,
            'totalAktif' => Cart::where('status', 'active')->count(),
            'totalDikembalikan' => Cart::where('status', 'returned')->count(),
            'totalReturStok' => (int) CartItem::whereHas('cart', fn ($c) => $c->where('status', 'returned'))->sum('qty_sisa'),
            'totalTerjual' => (int) CartItem::whereHas('cart', fn ($c) => $c->where('status', 'returned'))->sum('qty_terjual'),
        ]);
    }

    /**
     * Detail satu gerobak + komisi terkait (bila sudah dikembalikan).
     */
    public function show(Request $request, Cart $gerobak)
    {
        $gerobak->load(['user', 'items.product', 'commission.items']);

        return view('admin.carts.show', [
            'gerobak' => $gerobak,
            'komisi' => $gerobak->commission ?? new Commission,
        ]);
    }
}
