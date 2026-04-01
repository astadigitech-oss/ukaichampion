<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\UserResult;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public function with(): array
    {
        $userId = Auth::id();

        // Hitung statistik untuk Panel Atas
        $totalExams = UserResult::where('user_id', $userId)->whereNotNull('finished_at')->count();
        $averageScore = UserResult::where('user_id', $userId)->whereNotNull('finished_at')->avg('score') ?? 0;

        return [
            'histories' => UserResult::with(['examPackage.examCategory'])
                ->where('user_id', $userId)
                ->whereNotNull('finished_at')
                ->latest('finished_at')
                ->paginate(6), // 6 agar pas dengan grid 3 kolom
            'totalExams' => $totalExams,
            'averageScore' => $averageScore,
        ];
    }
}; ?>

<div>
    @if (session('error'))
        <div
            class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg shadow-sm font-bold flex items-center gap-2">
            <span>⚠️</span> {{ session('error') }}
        </div>
    @endif

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 border-l-4 border-l-green-500">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Nilai & Evaluasi</h2>
        <p class="text-gray-500 mt-1">Pantau terus perkembangan belajarmu dari hasil ujian yang telah diselesaikan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-t-4 border-t-blue-500">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Total Diselesaikan</h3>
            <p class="text-4xl font-extrabold text-gray-800 mt-2">{{ $totalExams }} <span
                    class="text-lg font-medium text-gray-500">paket</span></p>
        </div>

        <div
            class="bg-white p-6 rounded-xl shadow-sm border border-t-4 {{ $averageScore >= 70 ? 'border-t-green-500' : 'border-t-yellow-500' }}">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider mb-2">Rata-rata Nilai</h3>
            <p class="text-4xl font-extrabold {{ $averageScore >= 70 ? 'text-green-600' : 'text-yellow-600' }} mt-2">
                {{ number_format($averageScore, 1) }} <span class="text-lg font-medium text-gray-500">/ 100</span>
            </p>
        </div>
    </div>

    <h2 class="text-xl font-bold text-gray-800 mb-4">📈 Daftar Rekam Jejak Ujian</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($histories as $history)
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="p-6 grow">
                    <div class="flex justify-between items-start mb-4">
                        <span
                            class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $history->examPackage->examCategory->name }}
                        </span>
                        <span class="text-gray-400 text-sm font-medium flex items-center gap-1">
                            📅 {{ \Carbon\Carbon::parse($history->finished_at)->format('d M Y') }}
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $history->examPackage->title }}
                    </h3>

                    <div class="flex items-center gap-4 mt-6 bg-gray-50 p-3 rounded-lg border border-gray-100">
                        <div class="text-center grow">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Skor Akhir</p>
                            <p
                                class="text-3xl font-black {{ $history->score >= 70 ? 'text-green-600' : 'text-red-500' }}">
                                {{ number_format($history->score, 1) }}
                            </p>
                        </div>
                        <div class="h-10 w-px bg-gray-200"></div>
                        <div class="text-center grow">
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
                            <span
                                class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1.5 rounded-full inline-block mt-1">
                                ✅ Selesai
                            </span>
                        </div>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 bg-gray-50 text-center">
                    <span class="text-sm font-bold text-gray-500">
                        Selesai pukul: <span
                            class="text-gray-800">{{ \Carbon\Carbon::parse($history->finished_at)->format('H:i') }}
                            WIB</span>
                    </span>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-xl border border-gray-200 text-center shadow-sm">
                <span class="text-5xl mb-3 block">📭</span>
                <h3 class="text-xl font-bold text-gray-800">Belum ada riwayat ujian</h3>
                <p class="text-gray-500 mt-2">Nilai dan evaluasimu akan muncul di sini setelah kamu menyelesaikan paket
                    ujian.</p>
            </div>
        @endforelse
    </div>

    @if ($histories->hasPages())
        <div class="mt-8">
            {{ $histories->links() }}
        </div>
    @endif
</div>
