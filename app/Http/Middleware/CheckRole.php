<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Membatasi route berdasarkan role user ('admin' | 'employee').
 * Contoh: Route::middleware('role:admin')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Pastikan sudah login dan role user termasuk dalam daftar yang diizinkan
        if (! $request->user() || ! in_array($request->user()->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}