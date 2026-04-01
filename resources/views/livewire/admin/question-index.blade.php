<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use App\Models\Question;
use App\Models\ExamPackage;

new class extends Component {
    use WithPagination;

    public ExamPackage $package; // Menerima data paket dari halaman induk
    public $search = '';

    // VARIABEL BULK DELETE
    public $selected = [];
    public $selectAll = false;

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = Question::where('exam_package_id', $this->package->id)
                ->where('question_text', 'like', '%' . $this->search . '%')
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteSelected()
    {
        if (count($this->selected) > 0) {
            Question::whereIn('id', $this->selected)->delete();
            $this->selected = [];
            $this->selectAll = false;
            session()->flash('success', 'Butir soal terpilih berhasil dihapus.');
        }
    }

    public function delete($id)
    {
        Question::findOrFail($id)->delete();
        session()->flash('success', 'Butir soal berhasil dihapus.');
    }

    public function with(): array
    {
        return [
            // Hanya tampilkan soal yang dimiliki oleh paket ini
            'questions' => Question::where('exam_package_id', $this->package->id)
                ->where('question_text', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center bg-gray-50 flex-wrap gap-4">
        <div class="flex items-center gap-4 w-full md:w-auto">
            <div class="relative w-full max-w-md md:w-80">
                <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari potongan teks soal..."
                    class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-200 outline-none">
            </div>
            <div wire:loading class="text-blue-500 text-sm font-semibold animate-pulse">⏳ Mencari...</div>
        </div>

        @if (count($selected) > 0)
            <button wire:click="deleteSelected"
                wire:confirm="PERINGATAN! Anda yakin ingin menghapus {{ count($selected) }} soal terpilih secara permanen?"
                class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg shadow-lg transition-colors animate-pulse">
                🗑️ Hapus Terpilih ({{ count($selected) }})
            </button>
        @endif
    </div>
    <div class="p-6 bg-gray-50 border-b">
        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
            🧩 Peta Butir Soal
            <span class="normal-case font-medium text-gray-400">(Klik nomor untuk meluncur ke soal)</span>
        </h4>
        <div class="flex flex-wrap gap-2">
            @foreach ($questions as $index => $q)
                <button type="button" onclick="scrollToSoal('soal-{{ $q->id }}')"
                    class="w-10 h-10 flex items-center justify-center rounded-lg border bg-white text-sm font-bold text-gray-600 hover:bg-indigo-600 hover:text-white hover:border-indigo-600 transition-all shadow-sm">
                    {{ $questions->firstItem() + $index }}
                </button>
            @endforeach

            <a href="{{ route('admin.questions.create', ['package_id' => $package->id]) }}"
                class="w-10 h-10 flex items-center justify-center rounded-lg border border-dashed border-gray-400 text-gray-400 hover:bg-green-50 hover:text-green-600 hover:border-green-600 transition-all">
                ➕
            </a>
        </div>
    </div>
    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 m-4 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-white">
                <tr>
                    <th class="px-6 py-4 w-10 text-center">
                        <input type="checkbox" wire:model.live="selectAll"
                            class="w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                    </th>
                    <th class="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs w-16">No</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs">Cuplikan Soal</th>
                    <th class="px-6 py-4 text-center font-bold text-gray-500 uppercase text-xs">Kunci</th>
                    <th class="px-6 py-4 text-right font-bold text-gray-500 uppercase text-xs">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($questions as $index => $q)
                    <tr id="soal-{{ $q->id }}"
                        class="transition-colors scroll-mt-20 {{ in_array($q->id, $selected) ? 'bg-blue-50' : 'hover:bg-gray-50' }}">

                        <td class="px-6 py-4 text-center">
                            <input type="checkbox" wire:model.live="selected" value="{{ $q->id }}"
                                class="w-5 h-5 text-blue-600 border-gray-300 rounded cursor-pointer focus:ring-blue-500">
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $questions->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 line-clamp-2">{!! Str::limit(strip_tags($q->question_text), 100) !!}</div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span
                                class="bg-green-100 text-green-800 font-bold px-3 py-1 rounded-full text-xs shadow-sm">{{ $q->correct_answer }}</span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.questions.edit', $q->id) }}"
                                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded transition-colors mr-2 font-bold">Edit</a>
                            <button wire:click="delete({{ $q->id }})" wire:confirm="Hapus soal ini?"
                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded transition-colors font-bold">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-gray-500 text-lg">📭 Belum ada soal di
                            dalam paket ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t bg-gray-50">{{ $questions->links() }}</div>
</div>
<script>
    function scrollToSoal(elementId) {
        // Cari baris tabel berdasarkan ID
        const element = document.getElementById(elementId);

        if (element) {
            // 1. Tambahkan efek warna kuning (highlight) agar Admin tahu baris mana yang ditunjuk
            element.classList.add('bg-yellow-100');

            // 2. Lakukan scroll otomatis yang halus (Smooth Scroll)
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'center' // Soal akan diposisikan di tengah layar
            });

            // 3. Hilangkan warna kuning setelah 2 detik agar kembali normal
            setTimeout(() => {
                element.classList.remove('bg-yellow-100');
            }, 2000);
        } else {
            console.error('Elemen ' + elementId + ' tidak ditemukan di halaman ini.');
        }
    }
</script>
