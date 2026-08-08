<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Commission;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Alur gerobak karyawan:
 *  Ambil Gerobak -> pilih produk + jumlah -> stok berkurang -> dashboard
 *  Kembalikan Gerobak -> input sisa (retur) -> stok kembali -> bagi hasil 20% dihitung sistem
 */
class CartController extends Controller
{
    /**
     * Tampilkan form pilih produk saat "Ambil Gerobak".
     * 1 karyawan hanya boleh membawa 1 gerobak aktif.
     */
    public function tampilkanFormAmbil(Request $request)
    {
        $punyaGerobakAktif = Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->exists();

        if ($punyaGerobakAktif) {
            return redirect()->route('dashboard')
                ->with('error', 'Anda masih memiliki gerobak aktif. Kembalikan dulu sebelum mengambil gerobak baru.');
        }

        return view('carts.ambil', [
            'produk' => Product::where('stok', '>', 0)->orderBy('name')->get(),
        ]);
    }

    /**
     * Proses "Ambil Gerobak":
     * - buat cart + cart_items (snapshot harga modal & jual)
     * - kurangi stok produk sesuai qty yang diambil
     * Semua dalam 1 DB transaction agar konsisten.
     */
    public function ambilGerobak(Request $request)
    {
        $validated = $request->validate([
            'produk' => ['required', 'array', 'min:1'],
            'produk.*' => ['required', 'integer', 'exists:products,id'],
            'qty' => ['required', 'array'],
            'qty.*' => ['required', 'integer', 'min:1', 'max:9999'],
        ]);

        $user = $request->user();

        if (Cart::where('user_id', $user->id)->where('status', 'active')->exists()) {
            return back()->with('error', 'Anda masih punya gerobak aktif.');
        }

        return DB::transaction(function () use ($validated, $user) {
            $cart = Cart::create([
                'user_id' => $user->id,
                'status' => 'active',
                'taken_at' => now(),
            ]);

            foreach ($validated['produk'] as $productId) {
                $qty = (int) ($validated['qty'][$productId] ?? 0);
                if ($qty < 1) {
                    continue; // lewati baris yang tidak diisi jumlahnya
                }

                // lockForUpdate: kunci baris agar stok tidak "dobel ambil" saat bersamaan
                $produk = Product::lockForUpdate()->findOrFail($productId);

                if ($produk->stok < $qty) {
                    throw ValidationException::withMessages([
                        'qty.' . $productId => "Stok {$produk->name} tersisa {$produk->stok}.",
                    ]);
                }

                // Snapshot harga agar perhitungan bagi hasil tidak terpengaruh edit harga
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $produk->id,
                    'qty_ambil' => $qty,
                    'qty_sisa' => 0,
                    'qty_terjual' => 0,
                    'harga_modal' => $produk->harga_modal,
                    'harga_jual' => $produk->harga_jual,
                ]);

                // Produk keluar dari gudang
                $produk->decrement('stok', $qty);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Gerobak berhasil diambil. Selamat berjualan!');
        });
    }

    /**
     * Tampilkan form "Kembalikan Gerobak": user menginput jumlah sisa per produk.
     */
    public function tampilkanFormRetur(Request $request)
    {
        $gerobak = $this->gerobakAktifUser($request);
        if (! $gerobak) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada gerobak aktif.');
        }

        return view('carts.retur', [
            'gerobak' => $gerobak,
        ]);
    }

    /**
     * Proses "Kembalikan Gerobak" + retur stok + hitung bagi hasil otomatis.
     *
     * Langkah:
     * 1. Input sisa barang yang tidak terjual per item.
     * 2. qty_terjual = qty_ambil - qty_sisa.
     * 3. Retur: stok produk dikembalikan sebesar qty_sisa.
     * 4. Hitung keuntungan = (harga_jual - harga_modal) x qty_terjual.
     * 5. Upah karyawan (komisi) = 20% dari total keuntungan -> status pending.
     */
    public function returGerobak(Request $request)
    {
        $gerobak = $this->gerobakAktifUser($request);
        if (! $gerobak) {
            return redirect()->route('dashboard')->with('error', 'Tidak ada gerobak aktif.');
        }

        $validated = $request->validate([
            'sisa' => ['required', 'array'],
            'sisa.*' => ['required', 'integer', 'min:0', 'max:9999'],
        ]);

        return DB::transaction(function () use ($validated, $gerobak) {
            $totalPenjualan = 0;
            $totalModal = 0;

            foreach ($gerobak->items as $item) {
                $sisa = (int) ($validated['sisa'][$item->id] ?? 0);

                if ($sisa > $item->qty_ambil) {
                    throw ValidationException::withMessages([
                        'sisa.' . $item->id => 'Sisa tidak boleh melebihi jumlah diambil (' . $item->qty_ambil . ').',
                    ]);
                }

                $terjual = $item->qty_ambil - $sisa;

                // Catat hasil per item
                $item->update([
                    'qty_sisa' => $sisa,
                    'qty_terjual' => $terjual,
                ]);

                // RETUR TERINTEGRASI: barang sisa dikembalikan ke stok gudang
                if ($sisa > 0) {
                    $item->product->increment('stok', $sisa);
                }

                // Akumulasi nominal (harga snapshot pada saat ambil gerobak)
                $totalPenjualan += $item->subtotalPenjualan();
                $totalModal += $item->subtotalModal();
            }

            $totalUntung = $totalPenjualan - $totalModal;
            $upah = round($totalUntung * 0.20, 2); // bagi hasil 20% untuk karyawan

            // Simpan komisi (status pending -> menunggu diambil karyawan)
            Commission::create([
                'cart_id' => $gerobak->id,
                'user_id' => $gerobak->user_id,
                'total_penjualan' => $totalPenjualan,
                'total_modal' => $totalModal,
                'total_untung' => $totalUntung,
                'upah_20persen' => $upah,
                'status' => 'pending',
            ]);

            $gerobak->update([
                'status' => 'returned',
                'returned_at' => now(),
            ]);

            return redirect()->route('komisi.index')
                ->with('success', 'Gerobak dikembalikan. Keuntungan: Rp' . number_format($totalUntung, 0, ',', '.')
                    . ' — upah 20% Anda: Rp' . number_format($upah, 0, ',', '.') . '.');
        });
    }

    /**
     * Ambil gerobak aktif milik user yang sedang masuk, atau null bila tidak ada.
     */
    private function gerobakAktifUser(Request $request): ?Cart
    {
        return Cart::where('user_id', $request->user()->id)
            ->where('status', 'active')
            ->with('items.product')
            ->latest()
            ->first();
    }
}