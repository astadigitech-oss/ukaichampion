<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\UserResult;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{

    // Fungsi saat tombol "Mulai Kerjakan" ditekan
    public function startExam($package_id)
    {
        // KUNCI PERBAIKAN: Ambil data user langsung dari tabel Database 
        // menggunakan ID orang yang sedang login. Datanya pasti yang paling baru (fresh)!
        $user = \App\Models\User::find(Auth::id());

        // 1. AMBIL DATA PAKET TERLEBIH DAHULU
        $package = ExamPackage::withCount('questions')->findOrFail($package_id);

        // 2. CEK SOAL KOSONG: Jangan mulai jika paket belum ada soalnya
        if ($package->questions_count == 0) {
            return redirect()->route('user.exams')
                ->with('error', 'Paket ujian ini belum memiliki soal.');
        }

        // 3. CEK AKSES PREMIUM (Satu pesan error seragam untuk semua)
        if ($package->is_premium && !$user->is_premium) {
            return redirect()->route('user.exams')
                ->with('error', 'Akses ditolak! Anda harus Upgrade ke Premium untuk membuka ujian ini.');
        }

        // 4. CEK ATTEMPT: Cari tahu ini percobaan ke-berapa
        $lastAttempt = UserResult::where('user_id', $user->id)
            ->where('exam_package_id', $package_id)
            ->max('attempt_number');

        $currentAttempt = $lastAttempt ? $lastAttempt + 1 : 1;

        // 5. BUAT KERTAS UJIAN: Catat ke tabel USER_RESULTS
        $result = UserResult::create([
            'user_id'         => $user->id,
            'exam_package_id' => $package_id,
            'attempt_number'  => $currentAttempt,
            'score'           => 0, // Nilai awal 0
            'finished_at'     => null, // Null menandakan ujian sedang berlangsung
        ]);

        // 6. Arahkan ke Halaman Livewire Ujian yang sesungguhnya
        return redirect()->route('exam.play', $result->id);
    }

    public function play($result_id)
    {
        // Panggil halaman pembungkus ujian dan kirimkan ID result-nya
        return view('user.exam', compact('result_id'));
    }
}
