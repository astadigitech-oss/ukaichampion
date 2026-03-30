<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Menyuntikkan 1 akun Admin utama ke database
        Admin::create([
            'name' => 'dmin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin123'), // Ingat, passwordnya: admin123
        ]);
    }
}
