@extends('user.layouts.app')

@section('title', 'Tagihan Pembayaran - CBT APP')

@section('content')
    <div class="max-w-3xl mx-auto w-full mt-8 mb-16">

        @if (session('success'))
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg font-bold">
                ✅ {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-8 text-center text-white">
                <div class="inline-block bg-white/20 px-4 py-1.5 rounded-full text-sm font-bold tracking-widest mb-4">
                    INVOICE / TAGIHAN
                </div>
                <h2 class="text-3xl font-black mb-1">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</h2>
                <p class="text-blue-100 font-medium">Status:
                    @if ($transaction->status === 'success')
                        <span class="bg-green-400 text-green-900 px-2 py-0.5 rounded text-xs ml-1 font-bold">✅
                            BERHASIL</span>
                    @else
                        <span
                            class="bg-yellow-400 text-yellow-900 px-2 py-0.5 rounded text-xs ml-1 font-bold animate-pulse">⏳
                            PENDING</span>
                    @endif
                </p>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-2 gap-4 mb-8 border-b border-gray-100 pb-6">
                    <div>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">ID Transaksi</p>
                        <p class="text-lg font-mono font-bold text-gray-800">
                            #{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Tanggal</p>
                        <p class="text-base font-bold text-gray-800">{{ $transaction->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                    <div class="col-span-2 mt-4">
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">Pembelian</p>
                        <p class="text-lg font-bold text-gray-800">{{ $tierName }} (Aktif 30 Hari)</p>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-xl p-6 mb-8 border border-blue-100">
                    <h3 class="font-bold text-blue-900 mb-4 flex items-center gap-2">💳 Instruksi Pembayaran</h3>
                    <p class="text-sm text-blue-800 mb-4">Silakan transfer tepat <b>Rp
                            {{ number_format($transaction->amount, 0, ',', '.') }}</b> ke salah satu rekening berikut:</p>

                    <div class="space-y-3">
                        <div class="bg-white p-3 rounded-lg border border-blue-100 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 font-bold">Bank BCA</p>
                                <p class="font-mono font-bold text-lg text-gray-800">1234567890</p>
                            </div>
                            <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded text-gray-600">a.n Admin CBT</span>
                        </div>
                        <div class="bg-white p-3 rounded-lg border border-blue-100 flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 font-bold">Bank Mandiri</p>
                                <p class="font-mono font-bold text-lg text-gray-800">0987654321</p>
                            </div>
                            <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded text-gray-600">a.n Admin CBT</span>
                        </div>
                    </div>
                </div>

                @php
                    $waText =
                        "Halo Admin, saya ingin konfirmasi pembayaran Premium CBT.\n\n" .
                        'ID Transaksi: #' .
                        str_pad($transaction->id, 5, '0', STR_PAD_LEFT) .
                        "\n" .
                        'Nama Akun: ' .
                        auth()->user()->name .
                        "\n" .
                        'Email: ' .
                        auth()->user()->email .
                        "\n" .
                        'Total Transfer: Rp ' .
                        number_format($transaction->amount, 0, ',', '.') .
                        "\n\n" .
                        'Berikut saya lampirkan bukti transfernya.';
                @endphp

                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-3 font-medium">Sudah melakukan transfer? Konfirmasi sekarang!</p>
                    <a href="https://wa.me/{{ env('CBT_ADMIN_WA', '628000000') }}?text={{ urlencode($waText) }}"
                        target="_blank"
                        class="inline-flex items-center justify-center w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-green-200 transition-all gap-2 text-lg">
                        <span>💬</span> Kirim Bukti Transfer via WA
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection
