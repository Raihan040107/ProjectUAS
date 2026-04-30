<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('roles')->updateOrInsert(
            ['id_role' => 1],
            ['nama_role' => 'user']
        );

        DB::table('roles')->updateOrInsert(
            ['id_role' => 2],
            ['nama_role' => 'admin']
        );

        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'nama' => 'Test User',
                'username' => 'testuser',
                'password' => 'password',
                'id_role' => 1,
            ]
        );
    }
}
