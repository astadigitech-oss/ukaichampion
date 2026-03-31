<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\ExamPackage;
// Baris Request dan Auth dihapus karena belum/tidak dipakai di sini

class UserDashboardController extends Controller
{
    public function index()
    {
        // Ambil paket ujian dan hitung jumlah soalnya
        $packages = ExamPackage::with('examCategory')->withCount('questions')->latest()->get();

        return view('user.dashboard', compact('packages'));
    }
}
