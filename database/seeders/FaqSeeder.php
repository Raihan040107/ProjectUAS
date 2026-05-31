<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'question' => 'Apa saja syarat untuk mengajukan pendanaan hijau di F-Tech?',
                'answer'   => 'Anda hanya perlu menyiapkan dokumen legalitas usaha dasar, laporan operasional atau keuangan sederhana, dan mengisi kuesioner indikator hijau yang telah kami sediakan di platform.',
                'order'    => 1,
                'is_active' => true,
            ],
            [
                'question' => 'Berapa lama proses evaluasi skor ESG usaha saya?',
                'answer'   => 'Proses evaluasi dilakukan secara instan! Algoritma AI kami akan langsung mengalkulasi skor komitmen hijau Anda sesaat setelah Anda menyelesaikan form pertanyaan.',
                'order'    => 2,
                'is_active' => true,
            ],
            [
                'question' => 'Apakah data laporan keuangan dan operasional saya aman?',
                'answer'   => 'Sangat aman. Semua data yang Anda unggah dienkripsi dengan standar keamanan industri tingkat tinggi dan tidak akan disebarluaskan tanpa persetujuan eksplisit dari Anda.',
                'order'    => 3,
                'is_active' => true,
            ],
            [
                'question' => 'Bagaimana cara F-Tech menghubungkan bisnis saya dengan investor?',
                'answer'   => 'Setelah skor ESG Anda diterbitkan, profil bisnis hijau Anda akan otomatis masuk ke dalam direktori portofolio eksklusif yang diakses oleh puluhan bank dan lembaga pendanaan internasional mitra kami.',
                'order'    => 4,
                'is_active' => true,
            ],
        ];

        DB::table('faqs')->insert(array_map(function ($faq) {
            return array_merge($faq, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $faqs));
    }
}
