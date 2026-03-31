<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'is_premium' => 'nullable|boolean',
            'premium_until' => 'nullable|date',
        ]);

        // Proses data tambahan
        $validated['password'] = bcrypt($validated['password']);
        $validated['is_premium'] = $request->has('is_premium'); // Menghasilkan true/false

        // Jika bukan premium, hapus tanggalnya
        if (!$validated['is_premium']) {
            $validated['premium_until'] = null;
        }

        \App\Models\User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6', // Password boleh kosong jika tidak ingin diubah
            'is_premium' => 'nullable|boolean',
            'premium_until' => 'nullable|date',
        ]);

        // Logika update password hanya jika diisi
        if ($request->filled('password')) {
            $validated['password'] = bcrypt($request->password);
        } else {
            unset($validated['password']);
        }

        // Update status premium
        $validated['is_premium'] = $request->has('is_premium');
        if (!$validated['is_premium']) {
            $validated['premium_until'] = null;
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Data user berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
