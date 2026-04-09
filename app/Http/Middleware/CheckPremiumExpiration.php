<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User; // Pastikan Model User di-import di atas sini
use Symfony\Component\HttpFoundation\Response;

class CheckPremiumExpiration
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->is_premium && $user->premium_until) {

            if (Carbon::now()->greaterThan($user->premium_until)) {

                // 1. Ambil model dari DB dan langsung Update
                $updatedUser = User::find($user->id);
                $updatedUser->update([
                    'is_premium' => false,
                    'premium_until' => null,
                ]);

                // 2. PERBAIKAN DI SINI: Timpa data user di memori dengan data yang baru!
                // Ini menggantikan fungsi refresh() yang error
                Auth::setUser($updatedUser);

                session()->flash('error', 'Masa aktif Premium Anda telah habis. Anda sekarang kembali menjadi akun Reguler.');
            }
        }

        return $next($request);
    }
}
