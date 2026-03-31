<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\User;

new class extends Component {
    use WithPagination;
    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();
        session()->flash('success', 'Akun user berhasil dihapus.');
    }

    public function with(): array
    {
        return [
            // Mencari berdasarkan nama ATAU email
            'users' => User::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10),
        ];
    }
}; ?>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <div class="p-6 border-b flex justify-between items-center bg-gray-50">
        <div class="relative w-full max-w-md">
            <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari nama atau email user..."
                class="w-full px-4 py-2 pl-10 border rounded-lg focus:ring-2 focus:ring-blue-200 outline-none">
        </div>
        <div wire:loading class="text-blue-500 text-sm font-semibold animate-pulse">⏳ Mencari data...</div>
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
                    <th class="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs w-16">No</th>
                    <th class="px-6 py-4 text-left font-bold text-gray-500 uppercase text-xs">Informasi User</th>
                    <th class="px-6 py-4 text-center font-bold text-gray-500 uppercase text-xs">Status Akun</th>
                    <th class="px-6 py-4 text-right font-bold text-gray-500 uppercase text-xs">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $users->firstItem() + $index }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="shrink-0 h-10 w-10">
                                    <img class="h-10 w-10 rounded-full object-cover border"
                                        src="{{ $user->profile_picture ? asset('storage/' . $user->profile_picture) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF' }}"
                                        alt="">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($user->is_premium)
                                <span
                                    class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-bold border border-yellow-200 shadow-sm">👑
                                    PREMIUM</span>
                                <div class="text-xs text-gray-500 mt-1">s/d
                                    {{ \Carbon\Carbon::parse($user->premium_until)->format('d M Y') }}</div>
                            @else
                                <span
                                    class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">REGULER</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                                class="bg-yellow-100 text-yellow-700 hover:bg-yellow-200 px-3 py-1 rounded transition-colors mr-2">Edit</a>
                            <button wire:click="delete({{ $user->id }})"
                                wire:confirm="Hapus user ini beserta data ujiannya?"
                                class="bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 rounded transition-colors">Hapus</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-500 text-lg">📭 Belum ada data user
                            yang terdaftar.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4 border-t bg-gray-50">{{ $users->links() }}</div>
</div>
