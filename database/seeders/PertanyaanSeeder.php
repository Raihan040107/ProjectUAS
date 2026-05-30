<?php

namespace Database\Seeders;

use App\Models\Pertanyaan;
use Illuminate\Database\Seeder;

class PertanyaanSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            // ── Environment ──────────────────────────────────────────────
            [
                'pertanyaan' => 'Berapa rata-rata biaya konsumsi listrik operasional usaha Anda per bulan?',
                'aspek'      => 'environment',
                'urutan'     => 1,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Besar (> Rp 2 Juta / Bulan)',                'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Sedang (Rp 500 Ribu - Rp 2 Juta / Bulan)',  'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Efisien (< Rp 500 Ribu / Bulan)',            'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Apa sumber energi utama yang dominan digunakan dalam proses produksi / toko?',
                'aspek'      => 'environment',
                'urutan'     => 2,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Listrik PLN Murni',                                          'nilai' => 2],
                    ['label' => 'B', 'teks' => 'Masih bergantung Generator / Genset BBM',                    'nilai' => 1],
                    ['label' => 'C', 'teks' => 'Sudah kombinasi Energi Terbarukan (Solar Panel / dll)',       'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Bagaimana sistem pengelolaan sampah atau sisa limbah operasional Anda?',
                'aspek'      => 'environment',
                'urutan'     => 3,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Langsung dibuang gabung tanpa dipilah',                                   'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Dipilah berdasarkan kategori (Organik/Anorganik)',                         'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Didaur ulang / diolah mandiri bekerjasama dengan bank sampah',             'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Apakah usaha Anda menerapkan kebijakan kemasan hemat bahan baku eco-friendly?',
                'aspek'      => 'environment',
                'urutan'     => 4,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Belum sama sekali (masih plastik konvensional)',                           'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Sudah mulai mengurangi, beralih ke paperbag / totebag',                    'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Sudah tersertifikasi ramah lingkungan secara menyeluruh',                  'nilai' => 3],
                ],
            ],

            // ── Social ────────────────────────────────────────────────────
            [
                'pertanyaan' => 'Berapa total jumlah karyawan atau mitra kerja aktif di usaha Anda saat ini?',
                'aspek'      => 'social',
                'urutan'     => 1,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Mikro (1 - 5 Orang)',     'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Kecil (6 - 20 Orang)',    'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Menengah (> 20 Orang)',   'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Berapa persentase penyerapan tenaga kerja lokal dari wilayah sekitar tempat usaha?',
                'aspek'      => 'social',
                'urutan'     => 2,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Rendah (< 50% dari total staf)',                             'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Sangat Baik (50% - 80% warga lokal)',                        'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Prioritas Penuh (> 80% memberdayakan masyarakat sekitar)',   'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Apakah usaha Anda menyediakan jaminan keselamatan kesehatan (BPJS) bagi karyawan?',
                'aspek'      => 'social',
                'urutan'     => 3,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Belum tersedia',                                                          'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Hanya jaminan santunan kasual tidak formal jika terjadi musibah',         'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Sudah terdaftar jaminan kesehatan resmi (BPJS / Asuransi Staf)',          'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Bagaimana penataan regulasi jam kerja dan kesepakatan upah lembur karyawan?',
                'aspek'      => 'social',
                'urutan'     => 4,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Belum terstruktur secara formal (fleksibel mengikuti kondisi pasar)',     'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Jam kerja tetap teratur namun bonus lemburan belum dihitung detail',      'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Sudah memiliki sistem shift berkontrak tertulis yang disepakati layak',   'nilai' => 3],
                ],
            ],

            // ── Governance ────────────────────────────────────────────────
            [
                'pertanyaan' => 'Apa tingkat legalitas perizinan usaha resmi yang saat ini sudah Anda kantongi?',
                'aspek'      => 'governance',
                'urutan'     => 1,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Belum berizin resmi dagang',                                              'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Sudah memiliki NIB (Nomor Induk Berusaha) tingkat dasar',                 'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Lengkap (NIB, SIUP, NPWP Badan, atau Sertifikasi Halal/terkait)',        'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Bagaimana metode pencatatan arus keuangan kas harian usaha dilakukan?',
                'aspek'      => 'governance',
                'urutan'     => 2,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Dicatat manual di buku nota fisik biasa',                                 'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Rekap mingguan / bulanan menggunakan spreadsheet Excel biasa',            'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Sudah mengadopsi Aplikasi Pembukuan Finansial Digital khusus UMKM',       'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Apakah aset keuangan kas pribadi Anda terpisah rapi dengan kas operasional toko?',
                'aspek'      => 'governance',
                'urutan'     => 3,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Masih tercampur baur dalam satu rekening bank pribadi',                   'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Mayoritas terpisah namun pencatatan pengambilannya belum kaku',           'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Terpisah 100% mutlak dengan pembukuan kas yang profesional',              'nilai' => 3],
                ],
            ],
            [
                'pertanyaan' => 'Apakah usaha Anda memiliki target rencana tertulis jangka pendek maupun pembagian tugas?',
                'aspek'      => 'governance',
                'urutan'     => 4,
                'opsi'       => [
                    ['label' => 'A', 'teks' => 'Tidak ada (usaha berjalan mengalir natural saja)',                        'nilai' => 1],
                    ['label' => 'B', 'teks' => 'Ada rencana tertulis sederhana namun belum dievaluasi rutin berkala',     'nilai' => 2],
                    ['label' => 'C', 'teks' => 'Memiliki struktur organisasi dan target bisnis bulanan yang jelas',       'nilai' => 3],
                ],
            ],
        ];

        foreach ($data as $item) {
            $opsi = $item['opsi'];
            unset($item['opsi']);

            $pertanyaan = Pertanyaan::updateOrCreate(
                ['pertanyaan' => $item['pertanyaan']],
                [
                    'aspek' => $item['aspek'],
                    'urutan' => $item['urutan'],
                ]
            );

            $pertanyaan->opsiJawaban()->delete();

            foreach ($opsi as $o) {
                $pertanyaan->opsiJawaban()->create($o);
            }
        }
    }
}
