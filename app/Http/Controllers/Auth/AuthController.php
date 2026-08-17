<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Autentikasi custom (tanpa Breeze).
 * Registrasi publik SELALU membuat akun ber-role employee.
 * Akun admin dibuat lewat seeder / admin di dashboard.
 */
class AuthController extends Controller
{
    /** Tampilkan form login. */
    public function showLogin()
    {
        return view('auth.login');
    }

    /** Proses login. */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect sesuai role
            return Auth::user()->isAdmin()
                ? redirect()->route('admin.dashboard')
                : redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput($request->only('email'));
    }

    /** Tampilkan form register. */
    public function showRegister()
    {
        return view('auth.register');
    }

    /** Proses registrasi -> otomatis login sebagai employee. */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Role selalu employee untuk pendaftaran publik
        $user = User::create(array_merge($validated, ['role' => 'employee']));

        ActivityLogger::log($user->id, 'register', 'Mendaftarkan akun baru.');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil. Selamat datang, '.$user->name.'!');
    }

    /** Proses logout. */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }
}
