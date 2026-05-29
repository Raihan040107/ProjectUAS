<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom aspek dan urutan ke tabel pertanyaan yang sudah ada
        Schema::table('pertanyaan', function (Blueprint $table) {
            // aspek: 'environment' | 'social' | 'governance'
            $table->enum('aspek', ['environment', 'social', 'governance'])
                ->default('environment')
                ->after('pertanyaan');

            // urutan tampil di dalam aspek (1,2,3,4...)
            $table->unsignedTinyInteger('urutan')
                ->default(1)
                ->after('aspek');
        });

        // Buat tabel opsi_jawaban
        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id('opsi_id');

            $table->foreignId('pertanyaan_id')
                ->constrained('pertanyaan', 'pertanyaan_id')
                ->cascadeOnDelete();

            // Label huruf: A, B, C
            $table->char('label', 1);

            // Teks opsi yang ditampilkan ke user
            $table->string('teks');

            // Nilai skor ESG untuk opsi ini (1=rendah, 2=sedang, 3=tinggi)
            $table->unsignedTinyInteger('nilai')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opsi_jawaban');

        Schema::table('pertanyaan', function (Blueprint $table) {
            $table->dropColumn(['aspek', 'urutan']);
        });
    }
};
