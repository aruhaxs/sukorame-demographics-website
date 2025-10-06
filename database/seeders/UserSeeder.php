<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User; // <-- Import model User
use Illuminate\Support\Facades\Hash; // <-- Import Hash untuk enkripsi password

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('admin1234'),
        ]);

        // Anda bisa menambahkan user lain di sini jika perlu
        // User::create([
        //     'name' => 'User Biasa',
        //     'email' => 'user@contoh.com',
        //     'password' => Hash::make('password123'),
        // ]);
    }
}