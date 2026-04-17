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
        $user = \App\Models\User::find(Auth::id());

        // 1. AMBIL DATA PAKET TERLEBIH DAHULU
        $package = ExamPackage::withCount('questions')->findOrFail($package_id);

        // 2. CEK SOAL KOSONG
        if ($package->questions_count == 0) {
            return redirect()->route('user.exams')
                ->with('error', 'Paket ujian ini belum memiliki soal.');
        }

        // 3. CEK AKSES PREMIUM / KASTA
        // (Paket bawah tidak bisa akses paket atas)
        if ($package->is_premium && !$user->is_premium) {
            return redirect()->route('user.exams')
                ->with('error', 'Akses ditolak! Anda harus Upgrade paket akun Anda untuk membuka ujian ini.');
        }

        // 4. CEK UJIAN YANG MENGGANTUNG (MENCEGAH SPAM KLIK)
        $activeExam = UserResult::where('user_id', $user->id)
            ->where('exam_package_id', $package_id)
            ->whereNull('finished_at') // Cari yang sedang dikerjakan
            ->first();

        if ($activeExam) {
            // Jika ada ujian yang belum disubmit, kembalikan dia ke halaman ujian itu
            return redirect()->route('exam.play', $activeExam->id);
        }

        // 5. CEK BATAS PENGERJAAN (HANYA BOLEH 1 KALI)
        // Kita cek apakah user sudah PUNYA nilai (sudah selesai) di paket ini
        $hasFinished = UserResult::where('user_id', $user->id)
            ->where('exam_package_id', $package_id)
            ->whereNotNull('finished_at') // Cari yang statusnya sudah selesai
            ->exists();

        // Jika sudah pernah selesai, tolak siapapun itu (baik gratis maupun premium)
        if ($hasFinished) {
            return redirect()->route('user.exams')
                ->with('error', 'Maaf, Anda sudah pernah mengerjakan ujian ini. Setiap paket ujian hanya dapat dikerjakan 1 kali.');
        }

        // 6. BUAT KERTAS UJIAN BARU
        // Ambil durasi menit dari paket soal (pastikan kolom 'time_limit' ada di database-mu)
        $durasiMenit = $package->time_limit;

        $result = UserResult::create([
            'user_id'         => $user->id,
            'exam_package_id' => $package_id,
            'attempt_number'  => 1,
            'score'           => 0,
            'ends_at'         => now()->addMinutes($durasiMenit), // TAMBAHKAN INI: Batas waktu server
            'finished_at'     => null, // TETAP NULL: Karena ujian baru dimulai
        ]);

        // 7. Arahkan ke Halaman Livewire Ujian
        return redirect()->route('exam.play', $result->id);
    }

    public function play($result_id)
    {
        // Panggil halaman pembungkus ujian dan kirimkan ID result-nya
        return view('user.exam', compact('result_id'));
    }
}
