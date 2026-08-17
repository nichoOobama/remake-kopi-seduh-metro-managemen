<?php

namespace App\Observers;

use App\Models\Commission;
use App\Support\ActivityLogger;

/**
 * Catat aktivitas karyawan saat mengambil penghasilan (komisi pending -> paid).
 */
class CommissionObserver
{
    public function updated(Commission $commission): void
    {
        if ($commission->wasChanged('status') && $commission->status === 'paid') {
            ActivityLogger::log(
                $commission->user_id,
                'ambil_komisi',
                'Mengambil penghasilan Rp'.number_format($commission->upah_20persen, 0, ',', '.').'.'
            );
        }
    }
}
