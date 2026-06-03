<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Role
        DB::table('roles')->updateOrInsert(
            ['id_role' => 1],
            ['nama_role' => 'user']
        );

        DB::table('roles')->updateOrInsert(
            ['id_role' => 2],
            ['nama_role' => 'admin']
        );

        // User biasa
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'nama' => 'Test User',
                'username' => 'testuser',
                'password' => Hash::make('password'),
                'id_role' => 1,
            ]
        );

        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'id_role' => 2,
            ]
        );

        // Jalankan seeder pertanyaan + opsi jawaban
        $this->call([
            StudiKasusSeeder::class,
        ]);
    }
}
