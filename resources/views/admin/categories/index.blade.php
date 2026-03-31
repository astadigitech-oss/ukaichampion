<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Soal - CBT ADMIN</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>

<body class="bg-gray-100 flex min-h-screen">

    <div class="w-64 bg-gray-900 text-white flex flex-col shadow-xl">
        @include('admin.layouts.sidebar')
    </div>

    <div class="flex-1 p-8 h-screen flex flex-col">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-800">Manajemen Kategori Ujian</h1>
            <a href="{{ route('admin.categories.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg shadow transition-colors">
                + Tambah Kategori Baru
            </a>
        </div>

        <livewire:admin.category-index />

    </div>

    @livewireScripts
</body>

</html>
