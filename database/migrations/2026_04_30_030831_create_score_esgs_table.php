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
        Schema::create('score_esg', function (Blueprint $table) {
            $table->id('id_score'); // PK
            
            // Foreign Key
            $table->unsignedBigInteger('id_usaha');
            $table->foreign('id_usaha')->references('id_usaha')->on('usaha')->onDelete('cascade');
            
            $table->decimal('skor_environmental', 8, 2);
            $table->decimal('skor_social', 8, 2);
            $table->decimal('skor_governance', 8, 2);
            $table->decimal('skor_total', 8, 2);
            $table->string('kategori_skor');
            $table->date('tanggal_perhitungan');
            $table->decimal('skor_lama', 8, 2)->nullable();
            $table->decimal('skor_baru', 8, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('score_esg');
    }
};
