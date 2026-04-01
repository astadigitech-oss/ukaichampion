<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CBT Peserta')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 flex min-h-screen">

    <div class="w-64 bg-blue-800 text-white flex flex-col shadow-xl">
        <div class="p-6 text-center border-b border-blue-700">
            <h2 class="text-2xl font-extrabold tracking-wider">CBT PESERTA</h2>
        </div>
        <div class="grow p-4">
            <a href="{{ route('user.dashboard') }}"
                class="block py-3 px-4 rounded transition duration-200 mb-2 font-medium {{ request()->routeIs('user.dashboard') ? 'bg-blue-900 font-bold text-white' : 'hover:bg-blue-700 text-blue-100' }}">
                🏠 Dashboard Utama
            </a>

            <a href="#"
                class="block py-3 px-4 rounded transition duration-200 mb-2 font-medium hover:bg-blue-700 text-blue-100">
                📝 Daftar Ujian
            </a>

            <a href="{{ route('user.history') }}"
                class="block py-3 px-4 rounded transition duration-200 mb-2 font-medium {{ request()->routeIs('user.history') ? 'bg-blue-900 font-bold text-white' : 'hover:bg-blue-700 text-blue-100' }}">
                📊 Riwayat Nilai
            </a>

            <a href="#"
                class="block py-3 px-4 rounded transition duration-200 mb-2 font-medium hover:bg-blue-700 text-blue-100">
                💳 Upgrade Premium
            </a>
        </div>
        <div class="p-4 border-t border-blue-700">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded transition-colors shadow">
                    🚪 Keluar
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 flex flex-col h-screen overflow-hidden">

        <div class="bg-white shadow-sm p-4 flex justify-between items-center border-b border-gray-200 z-10">
            <h2 class="text-xl font-bold text-gray-800">Ruang Belajar</h2>
            <div class="flex items-center gap-3">
                <span class="text-gray-600 font-medium">{{ auth()->user()->name }}</span>
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold text-lg uppercase">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
            </div>
        </div>

        <div class="p-8 overflow-y-auto">
            @yield('content')
        </div>

    </div>

</body>

</html>
