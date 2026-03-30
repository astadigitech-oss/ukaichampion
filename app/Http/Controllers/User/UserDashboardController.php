<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        // Ambil data user yang sedang login saat ini
        $user = Auth::guard('web')->user();

        // Arahkan ke file tampilan (Blade) dan bawa data user tersebut
        return view('user.dashboard', compact('user'));
    }
}
