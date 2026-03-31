<div class="w-64 bg-gray-900 text-white flex flex-col shadow-xl">
    <div class="p-6 text-center border-b border-gray-800">
        <h2 class="text-2xl font-extrabold text-red-500">CBT ADMIN</h2>
    </div>
    <div class="grow p-4">
        <a href="{{ route('admin.dashboard') }}"
            class="block py-2.5 px-4 rounded transition duration-200 mb-2 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-800 border-l-4 border-red-500 font-bold' : 'hover:bg-gray-700' }}">
            📊 Dashboard
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="block py-2.5 px-4 rounded transition duration-200 mb-2 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-800 border-l-4 border-red-500 font-bold' : 'hover:bg-gray-700' }}">
            📚 Kategori Soal
        </a>

        <a href="{{ route('admin.packages.index') }}"
            class="block py-2.5 px-4 rounded transition duration-200 mb-2 {{ request()->routeIs('admin.packages.*') ? 'bg-gray-800 border-l-4 border-red-500 font-bold' : 'hover:bg-gray-700' }}">
            📦 Paket Ujian
        </a>

        <a href="{{ route('admin.users.index') }}"
            class="block py-2.5 px-4 rounded transition duration-200 mb-2 {{ request()->routeIs('admin.users.*') ? 'bg-gray-800 border-l-4 border-red-500 font-bold' : 'hover:bg-gray-700' }}">
            👥 Manajemen User
        </a>
    </div>
    <div class="p-4 border-t border-gray-800">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition-colors">Logout</button>
        </form>
    </div>
</div>
