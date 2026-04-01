<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Wajib ditambahkan untuk validasi Soft Delete

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Mengarahkan ke halaman Index (yang akan memanggil Livewire)
        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // Mengecek keunikan email, tapi abaikan yang sudah di-soft delete
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'password' => 'required|min:3',
            'is_premium' => 'nullable|boolean',
            'premium_until' => 'nullable|date',
        ], [
            // Pesan error kustom (Bahasa Indonesia)
            'email.unique' => 'Alamat email ini sudah terdaftar dan masih aktif.',
            'password.min' => 'Password minimal harus 3 karakter.'
        ]);

        // Proses data tambahan
        $validated['password'] = bcrypt($validated['password']);
        $validated['is_premium'] = $request->has('is_premium');

        // Jika bukan premium, hapus tanggalnya
        if (!$validated['is_premium']) {
            $validated['premium_until'] = null;
        }

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil didaftarkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Biasanya tidak dipakai karena detail user sudah ada di form Edit
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Cari data user lama, lalu kirim ke halaman form edit
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                // Abaikan email milik dia sendiri saat mengecek keunikan
                Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')
            ],
            'password' => 'nullable|min:3', // Boleh kosong jika admin tidak ingin meriset passwordnya
            'is_premium' => 'nullable|boolean',
            'premium_until' => 'nullable|date',
        ], [
            'email.unique' => 'Alamat email ini sudah dipakai oleh user lain.',
            'password.min' => 'Password minimal harus 3 karakter.'
        ]);

        // Cek apakah admin mengetikkan password baru
        if ($request->filled('password')) {
            // Jika diisi, enkripsi password barunya
            $validated['password'] = bcrypt($validated['password']);
        } else {
            // Jika kosong, hapus dari array agar password lama tidak tertimpa string kosong
            unset($validated['password']);
        }

        // Proses status premium
        $validated['is_premium'] = $request->has('is_premium');
        if (!$validated['is_premium']) {
            $validated['premium_until'] = null;
        }

        // Simpan perubahan ke database
        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Data profil user berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Karena fungsi Hapus kita dikendalikan oleh Livewire (user-index.blade.php), 
        // fungsi ini bisa kita jadikan cadangan (fallback) saja.
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }
}
