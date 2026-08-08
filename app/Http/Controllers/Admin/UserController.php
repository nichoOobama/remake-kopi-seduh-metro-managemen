<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Manajemen pengguna oleh admin:
 * - melihat daftar karyawan/admin,
 * - membuat akun baru (bisa pilih role admin/employee),
 * - menghapus akun (tidak bisa menghapus diri sendiri).
 */
class UserController extends Controller
{
    /** Daftar pengguna. */
    public function index()
    {
        return view('admin.users.index', [
            'users' => User::orderBy('role')->orderBy('name')->paginate(10),
        ]);
    }

    /** Buat akun baru oleh admin (role bebas dipilih admin). */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,employee'],
        ]);

        User::create($validated);

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun ' . $validated['name'] . ' berhasil dibuat.');
    }

    /** Hapus akun (manage sendiri tidak boleh). */
    public function destroy(Request $request, User $user)
    {
        abort_if($user->id === $request->user()->id, 400, 'Tidak bisa menghapus akun sendiri.');

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Akun ' . $user->name . ' dihapus.');
    }
}