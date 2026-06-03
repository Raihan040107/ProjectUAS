<?php

namespace Database\Seeders;

use App\Models\StudiKasus;
use Illuminate\Database\Seeder;

class StudiKasusSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nomor'      => '01',
                'nama_usaha' => 'Koperasi Tani Agro Lestari',
                'deskripsi'  => 'Mengajukan modal solar panel irigasi mandiri dengan melampirkan berkas sertifikasi organik bebas pestisida kimia berbahaya.',
                'pencapaian' => [
                    'Pendanaan Cair Rp850 Juta',
                    'Hemat Biaya Listrik Operasional 35%',
                ],
                'order'     => 1,
                'is_active' => true,
            ],
            [
                'nomor'      => '02',
                'nama_usaha' => 'PT Eco Plastik Manufaktur',
                'deskripsi'  => 'Melakukan pembuktian siklus daur ulang kemasan limbah polimer tinggi lewat sistem transparansi audit rantai pasok F-Tech.',
                'pencapaian' => [
                    'Kredit Sindikasi Rp4.2 Miliar',
                    '240 Ton Sampah Berhasil Didaur Ulang',
                ],
                'order'     => 2,
                'is_active' => true,
            ],
        ];

        foreach ($data as $item) {
            StudiKasus::create($item);
        }
    }
}
