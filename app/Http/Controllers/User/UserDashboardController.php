<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
use App\Models\UserResult;
use App\Models\Transaction; // TAMBAHAN PENTING UNTUK INVOICE
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // 1. Tentukan batas Kasta (Tier) apa saja yang boleh dilihat oleh pelanggan ini
        $allowedTiers = ['gratis']; // Default: Hanya boleh lihat yang gratis

        $isPremiumActive = $user->is_premium && $user->premium_until && now()->lessThanOrEqualTo($user->premium_until);

        if ($isPremiumActive) {
            if ($user->premium_tier === 'ultra') {
                $allowedTiers = ['gratis', 'plus', 'pro', 'ultra'];
            } elseif ($user->premium_tier === 'pro') {
                $allowedTiers = ['gratis', 'plus', 'pro'];
            } elseif ($user->premium_tier === 'plus') {
                $allowedTiers = ['gratis', 'plus'];
            }
        }

        // 2. Siapkan Query Dasar & Filter berdasarkan Kasta Pelanggan
        $query = ExamPackage::with('examCategory')
            ->withCount('questions')
            ->has('questions')
            ->whereHas('examCategory', function ($q) {
                $q->whereNull('deleted_at');
            })
            // TAMPILKAN ETALASE: Hanya paket yang sesuai dengan kasta pelanggan
            ->whereIn('minimum_tier', $allowedTiers);

        // 3. Eksekusi: Ambil 3 data terbaru
        $packages = $query->latest()->take(3)->get();

        // 4. Hitung ujian yang sudah selesai
        $completedExamsCount = UserResult::where('user_id', $user->id)
            ->whereNotNull('finished_at')
            ->count();

        // Kirim status isPremiumActive agar tampilan Blade lebih mudah
        return view('user.dashboard', compact('packages', 'completedExamsCount', 'isPremiumActive'));
    }

    public function exams()
    {
        return view('user.exams');
    }

    public function history()
    {
        return view('user.history');
    }

    public function profile()
    {
        return view('user.profile');
    }

    // Halaman Review Jawaban (Sistem Gembok Cerdas)
    public function review(Request $request, $id)
    {
        // 1. Ambil data pelanggan dan hasil ujian
        $user = \App\Models\User::find($request->user()->id);
        $result = UserResult::with(['examPackage.examCategory', 'userAnswers.question'])
            ->where('user_id', $user->id)
            ->findOrFail($id);

        // 2. Cek Kasta Paket Ujian tersebut
        $packageTier = $result->examPackage->minimum_tier;

        // 3. GEMBOK BACKEND: Gunakan fungsi pintar di Model User
        // Jika pelanggan turun kasta (misal dari Pro ke Gratis), dia tidak bisa lihat review soal Pro-nya lagi.
        if (!$user->canAccessTier($packageTier)) {
            return redirect()->route('user.history')
                ->with('error', 'Akses ditolak! Pembahasan ini eksklusif untuk kasta ' . ucfirst($packageTier) . '. Silakan Upgrade langganan Anda.');
        }

        return view('user.review', compact('result'));
    }

    public function upgrade()
    {
        return view('user.upgrade');
    }

    // =========================================================
    // FITUR BARU: CHECKOUT & INVOICE
    // =========================================================

    // PROSES PEMBUATAN TRANSAKSI
    // =========================================================
    // FITUR BARU: CHECKOUT & INVOICE
    // =========================================================

    // PROSES PEMBUATAN TRANSAKSI
    public function checkout(Request $request)
    {
        // PENGAMAN: Jika tiba-tiba sesi login habis
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $request->validate([
            'tier' => 'required|in:plus,pro,ultra'
        ]);

        // Tentukan harga berdasarkan kasta
        $prices = [
            'plus' => 50000,
            'pro' => 99000,
            'ultra' => 199000
        ];

        // Buat record transaksi di database dengan status pending
        $transaction = Transaction::create([
            'user_id' => $request->user()->id, // MENGGUNAKAN $request AGAR AMAN
            'amount' => $prices[$request->tier],
            'status' => 'pending',
        ]);

        // Lempar user ke halaman tagihan
        return redirect()->route('user.invoice', $transaction->id)->with('success', 'Pesanan berhasil dibuat! Selesaikan pembayaran Anda.');
    }

    // TAMPILKAN HALAMAN TAGIHAN
    public function invoice(Request $request, $id) // WAJIB TAMBAHKAN 'Request $request' DI SINI
    {
        // PENGAMAN: Jika tiba-tiba sesi login habis
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Cari transaksi milik user ini (menggunakan $request)
        $transaction = Transaction::where('user_id', $request->user()->id)->findOrFail($id);

        // Tentukan nama paket berdasarkan harga (untuk tampilan di invoice)
        $tierName = 'Paket PLUS';
        if ($transaction->amount == 99000) $tierName = 'Paket PRO';
        if ($transaction->amount == 199000) $tierName = 'Paket ULTRA';

        return view('user.invoice', compact('transaction', 'tierName'));
    }
}
