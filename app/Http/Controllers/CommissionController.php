<?php

namespace App\Http\Controllers;

use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Komisi (bagi hasil) karyawan.
 * Karyawan bisa melihat riwayat komisi dan "mengambil penghasilan"
 * (memindahkan komisi pending -> ke saldo, status jadi paid).
 */
class CommissionController extends Controller
{
    /** Riwayat komisi milik karyawan yang sedang login. */
    public function index(Request $request)
    {
        $user = $request->user();

        return view('commissions.index', [
            'komisi' => Commission::with('cart')
                ->where('user_id', $user->id)
                ->latest()
                ->get(),
            'totalPending' => (float) Commission::where('user_id', $user->id)
                ->where('status', 'pending')
                ->sum('upah_20persen'),
            'totalDiterima' => (float) Commission::where('user_id', $user->id)
                ->where('status', 'paid')
                ->sum('upah_20persen'),
        ]);
    }

    /**
     * Ambil penghasilan: komisi pending -> dipindah ke saldo user, status = paid.
     */
    public function ambil(Request $request, Commission $komisi)
    {
        // Hanya boleh mengambil komisi milik sendiri dan yang masih pending
        abort_if($komisi->user_id !== $request->user()->id, 403);
        abort_if($komisi->status === 'paid', 400, 'Komisi ini sudah diambil.');

        DB::transaction(function () use ($komisi, $request) {
            // Saldo penghasilan karyawan bertambah
            $request->user()->increment('balance', $komisi->upah_20persen);

            $komisi->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        return redirect()->route('komisi.index')
            ->with('success', 'Penghasilan Rp' . number_format($komisi->upah_20persen, 0, ',', '.') . ' berhasil diambil dan masuk ke saldo Anda.');
    }
}