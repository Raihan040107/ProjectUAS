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
         DB::table('pertanyaan')->insert([
            [
                'pertanyaan_id' => 1,
                'pertanyaan' => 'Apakah usaha Anda menghasilkan limbah? Jika iya, bagaimana pengelolaannya?',
            ],
            [
                'pertanyaan_id' => 2,
                'pertanyaan' => 'Apakah usaha Anda menggunakan bahan ramah lingkungan?',
            ],
            [
                'pertanyaan_id' => 3,
                'pertanyaan' => 'Apakah ada upaya pengurangan emisi atau energi?',
            ],
            [
                'pertanyaan_id' => 4,
                'pertanyaan' => 'Berapa jumlah karyawan yang Anda pekerjakan?',
            ],
            [
                'pertanyaan_id' => 5,
                'pertanyaan' => 'Apakah usaha Anda memberdayakan masyarakat lokal?',
            ],
            [
                'pertanyaan_id' => 6,
                'pertanyaan' => 'Apakah ada pelatihan untuk karyawan?',
            ],
            [
                'pertanyaan_id' => 7,
                'pertanyaan' => 'Apakah usaha Anda memiliki izin resmi?',
            ],
            [
                'pertanyaan_id' => 8,
                'pertanyaan' => 'Apakah laporan keuangan dicatat secara rutin?',
            ],
            [
                'pertanyaan_id' => 9,
                'pertanyaan' => 'Apakah ada struktur manajemen?',
            ],
            [
                'pertanyaan_id' => 10,
                'pertanyaan' => 'Berapa omzet bulanan usaha Anda?',
            ],
            [
                'pertanyaan_id' => 11,
                'pertanyaan' => 'Berapa biaya operasional per bulan?',
            ],
            [
                'pertanyaan_id' => 12,
                'pertanyaan' => 'Apakah usaha pernah mengambil pinjaman sebelumnya?',
            ],
            [
                'pertanyaan_id' => 13,
                'pertanyaan' => 'Berapa margin keuntungan rata-rata?',
            ],
        ]);

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

        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Admin',
                'username' => 'admin',
                'password' => 'password',
                'id_role' => 2,
            ]
        );
    }
}
