<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

/**
 * Pencatatan log aktivitas user (ringan, sinkron, tanpa queue).
 * Dipakai oleh observer model & listener autentikasi.
 */
class ActivityLogger
{
    /**
     * Catat aktivitas user.
     *
     * @param  int|null  $userId  Default: user yang sedang login.
     * @param  string  $action  Kode aksi, contoh: 'login', 'ambil_gerobak'.
     * @param  string|null  $description  Keterangan manusiawi.
     */
    public static function log(?int $userId, string $action, ?string $description = null): void
    {
        if (! $userId) {
            return;
        }

        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 255) ?: null,
        ]);
    }
}
