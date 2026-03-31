<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin CBT</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex">

    <div class="w-64 bg-gray-900 min-h-screen text-white flex flex-col">
        @include('admin.layouts.sidebar')
    </div>

    <div class="flex-1 p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Overview Sistem</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Total Pengguna</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">0</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Pendapatan</h3>
                <p class="text-3xl font-bold text-gray-800 mt-2">Rp 0</p>
            </div>
        </div>
    </div>

</body>

</html>
