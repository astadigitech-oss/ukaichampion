<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->whereNull('deleted_at')
            ],
            'password' => 'required|min:3',
            'premium_tier' => 'required|in:gratis,plus,pro,ultra',
            'premium_until' => 'nullable|date',
        ], [
            'email.unique' => 'Alamat email ini sudah terdaftar dan masih aktif.',
            'password.min' => 'Password minimal harus 3 karakter.'
        ]);

        $validated['password'] = bcrypt($validated['password']);

        // LOGIKA KASTA BARU
        if ($validated['premium_tier'] === 'gratis') {
            $validated['is_premium'] = false;
            $validated['premium_until'] = null;
        } else {
            $validated['is_premium'] = true;
            // Jika tanggal dikosongkan tapi kastanya bayar, otomatis set 1 bulan
            if (empty($validated['premium_until'])) {
                $validated['premium_until'] = now()->addDays(30);
            }
        }

        User::create($validated);

        return redirect()->route('admin.users.index')->with('success', 'User baru berhasil didaftarkan!');
    }

    public function show(string $id)
    {
        // Tidak dipakai
    }

    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)->whereNull('deleted_at')
            ],
            'password' => 'nullable|min:3',
            'premium_tier' => 'required|in:gratis,plus,pro,ultra',
            'premium_until' => 'nullable|date',
        ], [
            'email.unique' => 'Alamat email ini sudah dipakai oleh user lain.',
            'password.min' => 'Password minimal harus 3 karakter.'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']);
        }

        // ==========================================
        // LOGIKA KASTA BARU
        // ==========================================
        if ($validated['premium_tier'] === 'gratis') {
            $validated['is_premium'] = false;
            $validated['premium_until'] = null;
        } else {
            $validated['is_premium'] = true;

            // Jika Admin mengosongkan input tanggal, otomatis diset 1 Tahun
            if (empty($validated['premium_until'])) {
                $validated['premium_until'] = now()->addYear(); // <--- INI YANG DIUBAH
            }
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')->with('success', 'Data profil user berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus.');
    }

    // ==========================================
    // FUNGSI DARURAT: RESET UJIAN MURID (VERSI FIX 1000%)
    // ==========================================
    public function resetUjianMurid($result_id)
    {
        // 1. Cari data hasil ujian murid berdasarkan ID-nya
        $result = \App\Models\UserResult::findOrFail($result_id);

        // 2. Hapus semua jawaban yang sudah terlanjur dia pilih
        \App\Models\UserAnswer::where('result_id', $result_id)->delete();

        // 3. Hapus memori nomor soal terakhir di session server
        session()->forget('last_q_' . $result_id);

        // Hapus juga sisa-sisa jawaban yang mungkin masih nyangkut di Redis
        \Illuminate\Support\Facades\Redis::del('exam_answers:' . $result_id);

        // 4. KUNCI SUKSES: Ubah data satu per satu agar tidak diblokir Laravel
        $result->score = null;
        $result->finished_at = null;
        $result->created_at = now(); // Ini pasti akan mengubah waktunya ke detik ini

        // Cek secara aman apakah kolom ends_at benar-benar ada di tabel database
        if (\Illuminate\Support\Facades\Schema::hasColumn('user_results', 'ends_at')) {
            $result->ends_at = null;
        }

        // 5. Simpan paksa ke database
        $result->save();

        return redirect()->back()->with('success', 'Ujian berhasil direset total! Waktu pengerjaan diulang dari awal.');
    }
}
