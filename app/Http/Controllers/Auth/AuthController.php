<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // 1. Logika Register User Baru
    public function register(Request $request)
    {
        // Validasi data yang diinput user
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:3',
        ]);

        // Masukkan data ke tabel users
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password
            'is_premium' => false, // Aturan bisnis: Pendaftar baru selalu Gratis
        ]);

        // Otomatis login setelah register berhasil
        Auth::guard('web')->login($user);

        // Arahkan ke halaman dashboard user
        return redirect()->route('user.dashboard');
    }

    // 2. Logika Login User Biasa
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek kecocokan di tabel users menggunakan guard 'web'
        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate(); // Cegah serangan keamanan session fixation
            return redirect()->route('user.dashboard');
        }

        // Jika salah, kembalikan ke halaman sebelumnya dengan pesan error
        return back()->withErrors([
            'email' => 'Email atau password yang kamu masukkan salah.',
        ]);
    }

    // 3. Logika Login Admin
    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Cek kecocokan di tabel admins menggunakan guard 'admin'
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password admin salah.',
        ]);
    }

    // 4. Logika Logout (Global untuk Admin dan User)
    public function logout(Request $request)
    {
        // Cek siapa yang sedang login, lalu keluarkan
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } else {
            Auth::guard('web')->logout();
        }

        // Bersihkan seluruh data sesi agar aman
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // Arahkan kembali ke halaman utama
    }
}
