<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\ExamPackage;
use App\Models\ExamCategory;
use App\Models\UserResult;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $selectedCategory = ''; // Variabel filter kategori

    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $userId = Auth::id();

        $packages = ExamPackage::with('examCategory')
            // KUNCI PERBAIKAN: Wajibkan paket memiliki kategori yang tidak di-Soft Delete
            ->whereHas('examCategory', function ($query) {
                $query->whereNull('deleted_at');
            })
            // Lanjut filter dropdown kategori
            ->when($this->selectedCategory, function ($query) {
                $query->where('exam_category_id', $this->selectedCategory);
            })
            // Lanjut filter pencarian
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')->orWhereHas('examCategory', function ($q) {
                    $q->whereNull('deleted_at')->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(9);

        $packageIds = $packages->pluck('id');

        $allUserResults = UserResult::where('user_id', $userId)->whereIn('exam_package_id', $packageIds)->whereNotNull('finished_at')->latest('finished_at')->get()->groupBy('exam_package_id');

        return [
            'packages' => $packages,
            'userResults' => $allUserResults,
            'categories' => ExamCategory::all(),
        ];
    }
}; ?>

<div>
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Katalog Ujian</h2>
            <p class="text-gray-500 mt-1">Pilih dan kerjakan paket ujian untuk mengasah kemampuanmu.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
            <select wire:model.live="selectedCategory"
                class="px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 outline-none shadow-sm bg-white text-gray-700 font-medium">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <div class="relative w-full sm:w-64">
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama paket..."
                    class="w-full px-4 py-2 pl-10 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-200 outline-none shadow-sm">
            </div>
        </div>
    </div>

    <div wire:loading class="w-full mb-4 text-center text-blue-500 text-sm font-bold animate-pulse">
        ⏳ Memuat data...
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($packages as $package)
            @php $recentScores = $userResults->get($package->id, collect())->take(3); @endphp
            <div
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col">
                <div class="p-6 grow flex flex-col">
                    <div class="flex justify-between items-start mb-4">
                        <span
                            class="bg-blue-50 text-blue-600 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ $package->examCategory?->name ?? 'Tanpa Kategori' }}
                        </span>
                        <span class="text-gray-400 text-sm font-medium flex items-center gap-1">⏱️
                            {{ $package->time_limit }} Menit</span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2">{{ $package->title }}</h3>
                    <div class="text-sm text-gray-500 mt-1 mb-4 flex-grow">📝 {{ $package->questions_count ?? 0 }} Butir
                        Soal</div>

                    @if ($recentScores->count() > 0)
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Riwayat Nilai
                                Terakhir:</p>
                            <div class="flex gap-2 flex-wrap">
                                @foreach ($recentScores as $score)
                                    <span
                                        class="px-2 py-1 text-xs font-black rounded-md border {{ $score->score >= 70 ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}"
                                        title="Selesai pada: {{ \Carbon\Carbon::parse($score->finished_at)->format('d M Y') }}">
                                        {{ number_format($score->score, 1) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            <p class="text-xs font-medium text-gray-400 italic">Belum pernah dikerjakan</p>
                        </div>
                    @endif
                </div>
                <div class="p-4 bg-gray-50 border-t border-gray-100">
                    <form action="{{ route('exam.start', $package->id) }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit"
                            class="w-full text-center {{ $recentScores->count() > 0 ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-bold py-2.5 px-4 rounded-lg transition-colors shadow-sm">
                            {{ $recentScores->count() > 0 ? '🔄 Kerjakan Ulang' : '🚀 Mulai Kerjakan' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-xl border text-center shadow-sm"><span
                    class="text-4xl mb-3 block">📭</span>
                <h3 class="text-lg font-bold text-gray-800">Paket ujian tidak ditemukan</h3>
            </div>
        @endforelse
    </div>
    @if ($packages->hasPages())
        <div class="mt-8">{{ $packages->links() }}</div>
    @endif
</div>
