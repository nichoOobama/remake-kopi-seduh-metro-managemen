<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

/**
 * Log aktivitas user (khusus admin, READ-ONLY).
 * SENG AJA tanpa route create/update/delete — hanya index (baca).
 */
class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $aksi = trim((string) $request->query('aksi'));
        $cari = trim((string) $request->query('q'));
        $dari = $request->query('dari');
        $sampai = $request->query('sampai');

        $logs = ActivityLog::query()
            ->with('user')
            ->when($aksi !== '', fn ($q) => $q->where('action', $aksi))
            ->when($cari !== '', fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$cari}%"))
                ->orWhere('description', 'like', "%{$cari}%"))
            ->when($dari, fn ($q) => $q->whereDate('created_at', '>=', $dari))
            ->when($sampai, fn ($q) => $q->whereDate('created_at', '<=', $sampai))
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.activity.index', [
            'logs' => $logs,
            'aksi' => $aksi,
            'cari' => $cari,
            'dari' => $dari,
            'sampai' => $sampai,
            'daftarAksi' => ActivityLog::distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
