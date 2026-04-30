<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuan', function (Blueprint $table) {
            $table->id('id_pengajuan'); // PK
            
            // Foreign Key
            $table->unsignedBigInteger('id_usaha');
            $table->foreign('id_usaha')->references('id_usaha')->on('usaha')->onDelete('cascade');
            
            $table->string('perubahan');
            $table->text('text_saran');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor_bulan');
            $table->decimal('bunga_diterapkan', 5, 2);
            $table->decimal('tingkat_bunga_khusus', 5, 2);
            $table->integer('skor_esg_minimum');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};
