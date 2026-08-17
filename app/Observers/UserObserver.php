<?php

namespace App\Observers;

use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Auth;

/**
 * Catat aktivitas admin saat mengelola akun user.
 */
class UserObserver
{
    public function created(User $user): void
    {
        if (! Auth::check() || Auth::id() === $user->id) {
            return; // registrasi publik dicatat di AuthController
        }

        ActivityLogger::log(Auth::id(), 'tambah_user', "Membuat akun {$user->name} ({$user->role}).");
    }

    public function deleted(User $user): void
    {
        if (Auth::id() === $user->id) {
            return;
        }

        ActivityLogger::log(Auth::id(), 'hapus_user', "Menghapus akun {$user->name}.");
    }
}
