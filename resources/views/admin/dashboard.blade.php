@extends('admin.layouts.sidebar')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Overview Sistem</h1>
        <p class="text-gray-500 mt-1">Selamat datang kembali di panel kendali CBT.</p>
    </div>

    @if (session('success'))
        <div
            class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg shadow-sm font-bold flex items-center gap-2">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-blue-500 hover:shadow-md transition-shadow">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Pengguna</h3>
            <div class="flex items-end gap-2 mt-2">
                <p class="text-4xl font-black text-gray-800">{{ $totalUsers ?? 0 }}</p>
                <p class="text-gray-400 text-sm mb-1 font-medium">Siswa</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-purple-500 hover:shadow-md transition-shadow">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Total Paket Ujian</h3>
            <div class="flex items-end gap-2 mt-2">
                <p class="text-4xl font-black text-gray-800">{{ $totalPackages ?? 0 }}</p>
                <p class="text-gray-400 text-sm mb-1 font-medium">Paket</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-t-4 border-green-500 hover:shadow-md transition-shadow">
            <h3 class="text-gray-500 text-sm font-bold uppercase tracking-wider">Estimasi Pendapatan</h3>
            <p class="text-3xl font-black text-gray-800 mt-2">Rp {{ number_format($revenue ?? 0, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="mt-10 bg-white p-8 rounded-xl border border-gray-100 shadow-sm">
        <h3 class="font-bold text-gray-700 mb-4">Aktivitas Terakhir</h3>
        <p class="text-sm text-gray-400 italic">Belum ada aktivitas terbaru untuk ditampilkan.</p>
    </div>
@endsection
