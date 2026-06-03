<?php

namespace Database\Seeders;

use App\Models\Keunggulan;
use Illuminate\Database\Seeder;

class KeunggulanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nomor'     => '01',
                'judul'     => 'Suku Bunga Preferensial',
                'deskripsi' => 'Dapatkan keringanan nilai suku bunga pinjaman modal yang jauh lebih rendah bagi bisnis dengan skor komitmen pelestarian hijau yang tinggi.',
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'nomor'     => '02',
                'judul'     => 'Pelaporan Otomatis',
                'deskripsi' => 'Sistem kami akan mengekstrak data dari dokumen laporan operasional harian Anda menjadi berkas siap pakai untuk dikirimkan langsung ke investor.',
                'order'     => 2,
                'is_active' => true,
            ],
            [
                'nomor'     => '03',
                'judul'     => 'Ekosistem Investor Luas',
                'deskripsi' => 'Tembus akses pendanaan lintas negara ke puluhan lembaga perbankan korporasi dan institusi Ventura yang mencari portfolio berdampak iklim positif.',
                'order'     => 3,
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            Keunggulan::create($item);
        }
    }
}
