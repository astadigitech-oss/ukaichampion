@extends('admin.layouts.sidebar')

@section('content')
    <div class="max-w-4xl mx-auto w-full">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Daftarkan User Baru</h1>
            <a href="{{ route('admin.users.index') }}"
                class="text-gray-500 hover:text-gray-800 font-semibold transition-colors">
                ✕ Batal
            </a>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST"
            class="bg-white p-8 rounded-xl shadow-lg border-t-4 border-blue-600">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nama Lengkap <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="w-full px-4 py-2 border @error('name') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-200 outline-none"
                        required placeholder="Contoh: Budi Santoso">
                    @error('name')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Alamat Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-2 border @error('email') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-200 outline-none"
                        required placeholder="budi@email.com">
                    @error('email')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2 border @error('password') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-blue-200 outline-none"
                        required placeholder="Minimal 3 karakter">
                    @error('password')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Tingkat Langganan (Kasta)</label>
                    <select name="premium_tier" id="premium_tier" onchange="togglePremiumDate()"
                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-yellow-200 outline-none bg-yellow-50 font-bold text-gray-800">
                        <option value="gratis" {{ old('premium_tier') == 'gratis' ? 'selected' : '' }}>🆓 Akun Gratis
                        </option>
                        <option value="plus" {{ old('premium_tier') == 'plus' ? 'selected' : '' }}>✨ Plus Member</option>
                        <option value="pro" {{ old('premium_tier') == 'pro' ? 'selected' : '' }}>👑 Pro Member</option>
                        <option value="ultra" {{ old('premium_tier') == 'ultra' ? 'selected' : '' }}>🔮 Ultra Member
                        </option>
                    </select>
                </div>

                <div id="premium_date_container"
                    class="mb-4 {{ old('premium_tier') && old('premium_tier') != 'gratis' ? '' : 'hidden' }}">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Berlaku Hingga</label>
                    <input type="date" name="premium_until" value="{{ old('premium_until') }}"
                        class="w-full px-4 py-2 border @error('premium_until') border-red-500 bg-red-50 @else border-gray-300 @enderror rounded-lg focus:ring-2 focus:ring-yellow-200 outline-none">
                    @error('premium_until')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-[10px] text-gray-500 mt-1 italic">*Kosongkan untuk otomatis 30 hari.</p>
                </div>
            </div>

            <hr class="my-6">

            <div class="flex justify-end gap-4">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-lg shadow-lg transition-colors">
                    💾 Simpan Data User
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePremiumDate() {
            const selectBox = document.getElementById('premium_tier');
            const container = document.getElementById('premium_date_container');

            if (selectBox.value !== 'gratis') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
@endsection
