@extends('user.layouts.app')

@section('content')
    <div class="pb-8">

        {{-- Header dengan proporsi normal dan elegan --}}
        <div class="mb-8 text-center md:text-left">
            <h1 class="text-3xl font-black text-gray-900">Upgrade Langganan</h1>
            <p class="text-gray-500 mt-2 text-sm">Pilih paket terbaik untuk membuka ribuan soal dan fitur eksklusif.</p>
            <div
                class="mt-4 inline-block bg-blue-50 border border-blue-100 text-blue-800 text-sm font-bold px-4 py-2 rounded-lg">
                🏦 Seabank : 9018-3663-4869
            </div>
        </div>

        {{-- Grid dengan jarak (gap-8) yang memberikan ruang napas --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            {{-- PAKET PLUS --}}
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-blue-50 to-white">
                    <span
                        class="bg-blue-100 text-blue-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">✨
                        Plus</span>
                    <h3 class="text-xl font-bold text-gray-900 mt-4 mb-1">Paket Dasar</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-gray-900">Rp 9.999</span>
                        <span class="text-gray-500 font-medium text-sm">/tahun</span>
                    </div>
                </div>
                <div class="p-6 grow">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-start gap-2"><span class="text-green-500">✓</span> Total 300 soal</li>
                        <li class="flex items-start gap-2"><span class="text-green-500">✓</span> 1 paket 100 soal free</li>
                        <li class="flex items-start gap-2"><span class="text-green-500">✓</span> 2 paket 50 soal plus</li>
                        <li class="flex items-start gap-2"><span class="text-green-500">✓</span> 1 paket 100 soal plus</li>
                    </ul>
                </div>
                <div class="p-6 pt-0 mt-auto">
                    <form action="{{ route('user.checkout') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" value="plus">
                        <button type="submit"
                            class="block w-full py-3 px-4 text-center rounded-xl font-bold transition-colors bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white border border-blue-200 hover:border-transparent">
                            Pilih Paket Plus
                        </button>
                    </form>
                </div>
            </div>

            {{-- PAKET PRO --}}
            <div
                class="bg-white rounded-2xl shadow-sm border border-yellow-300 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300">
                <div class="p-6 border-b border-gray-100 bg-gradient-to-br from-yellow-50 to-white">
                    <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider">👑
                        Pro</span>
                    <h3 class="text-xl font-bold text-gray-900 mt-4 mb-1">Paket Intensif</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-gray-900">Rp 29.999</span>
                        <span class="text-gray-500 font-medium text-sm">/tahun</span>
                    </div>
                </div>
                <div class="p-6 grow">
                    <ul class="space-y-3 text-gray-600 text-sm font-medium">
                        <li class="flex items-start gap-2"><span class="text-yellow-500">✓</span> Total 700 soal</li>
                        <li class="flex items-start gap-2"><span class="text-yellow-500">✓</span> 1 paket 100 soal free</li>
                        <li class="flex items-start gap-2"><span class="text-yellow-500">✓</span> 4 paket 50 soal pro</li>
                        <li class="flex items-start gap-2"><span class="text-yellow-500">✓</span> 4 paket 100 soal pro</li>
                    </ul>
                </div>
                <div class="p-6 pt-0 mt-auto">
                    <form action="{{ route('user.checkout') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" value="pro">
                        <button type="submit"
                            class="block w-full py-3 px-4 text-center rounded-xl font-bold transition-colors bg-yellow-50 text-yellow-800 hover:bg-yellow-400 hover:text-yellow-900 border border-yellow-300 hover:border-transparent">
                            Pilih Paket Pro
                        </button>
                    </form>
                </div>
            </div>

            {{-- PAKET ULTRA --}}
            <div
                class="bg-gray-900 rounded-2xl shadow-sm border border-gray-800 overflow-hidden flex flex-col hover:shadow-xl transition-all duration-300">
                <div class="p-6 border-b border-gray-800 bg-gradient-to-br from-purple-900 to-gray-900">
                    <span
                        class="bg-purple-800 text-purple-100 text-xs font-extrabold px-3 py-1 rounded-full uppercase tracking-wider border border-purple-600">🔮
                        Ultra</span>
                    <h3 class="text-xl font-bold text-white mt-4 mb-1">Paket Sultan</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="text-3xl font-black text-white">Rp 49.999</span>
                        <span class="text-gray-400 font-medium text-sm">/tahun</span>
                    </div>
                </div>
                <div class="p-6 grow">
                    <ul class="space-y-3 text-gray-300 text-sm font-medium">
                        <li class="flex items-start gap-2"><span class="text-purple-400">✓</span> Total 1200 soal</li>
                        <li class="flex items-start gap-2"><span class="text-purple-400">✓</span> 1 paket 100 soal gratis
                        </li>
                        <li class="flex items-start gap-2"><span class="text-purple-400">✓</span> 8 paket 50 soal ultra</li>
                        <li class="flex items-start gap-2"><span class="text-purple-400">✓</span> 6 paket 100 soal ultra
                        </li>
                        <li class="flex items-start gap-2"><span class="text-purple-400">✓</span> 1 paket 200 Soal Ultra
                        </li>
                    </ul>
                </div>
                <div class="p-6 pt-0 mt-auto">
                    <form action="{{ route('user.checkout') }}" method="POST" class="m-0">
                        @csrf
                        <input type="hidden" name="tier" value="ultra">
                        <button type="submit"
                            class="block w-full py-3 px-4 text-center rounded-xl font-bold transition-colors bg-purple-600 text-white hover:bg-purple-500 border border-purple-500 hover:border-transparent">
                            Pilih Paket Ultra
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
