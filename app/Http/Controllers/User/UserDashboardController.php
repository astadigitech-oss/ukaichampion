<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\UserResult;
use Illuminate\Http\Request; // 1. TAMBAHKAN INI UNTUK REQUEST

class UserDashboardController extends Controller
{
    // 2. TANGKAP $request DI SINI
    public function index(Request $request)
    {
        // Pengaman: Jika tiba-tiba user belum login tapi tersasar ke sini
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // 1. Siapkan Query Dasar (Gabungan kodemu & logika Task 3)
        $query = ExamPackage::with('examCategory')
            ->withCount('questions')
            ->has('questions') // TASK 3: Wajib punya minimal 1 soal
            ->whereHas('examCategory', function ($q) {
                $q->whereNull('deleted_at'); // Mempertahankan kodemu yang bagus ini
            });

        // 2. TASK 3: Filter Kasta (Freemium)
        // Jika user BUKAN premium, saring HANYA TAMPILKAN paket yang gratis
        if (!$user->is_premium) {
            $query->where('is_premium', false);
        }

        // 3. Eksekusi: Ambil 3 data terbaru
        $packages = $query->latest()->take(3)->get();

        // 4. Hitung ujian yang sudah selesai
        $completedExamsCount = UserResult::where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->count();

        return view('user.dashboard', compact('packages', 'completedExamsCount'));
    }

    public function exams()
    {
        return view('user.exams');
    }

    // Halaman Riwayat Nilai
    public function history()
    {
        return view('user.history');
    }

    // Halaman Edit Profil
    public function profile()
    {
        return view('user.profile');
    }

    // Halaman Review Jawaban (LOGIKA PINDAH KE SINI)
    public function review(Request $request, $id)
    {
        // 1. GEMBOK BACKEND: Ambil data user paling fresh dari database
        $user = \App\Models\User::find($request->user()->id);

        // 2. TENDANG JIKA BUKAN PREMIUM
        if (!$user->is_premium) {
            return redirect()->route('user.history') // Lempar kembali ke halaman history
                ->with('error', 'Akses ditolak! Anda harus Upgrade ke Premium untuk melihat pembahasan soal.');
        }

        // 3. KODE ASLI: Pastikan user hanya bisa melihat hasil miliknya sendiri
        $result = UserResult::with(['examPackage.examCategory', 'userAnswers.question'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        return view('user.review', compact('result'));
    }

    public function upgrade()
    {
        // Akan memanggil file resources/views/user/upgrade.blade.php
        return view('user.upgrade');
    }
}
