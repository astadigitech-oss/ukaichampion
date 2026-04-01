@extends('admin.layouts.sidebar')

@section('content')
    {{-- Semua kode di sini akan masuk ke bagian @yield('content') di sidebar --}}
    <div class="p-4 md:p-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Profil</h1>
        </div>

        @livewire('admin.profile-edit')
    </div>
@endsection
