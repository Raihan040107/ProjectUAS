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
        Schema::create('jawaban', function (Blueprint $table) {
            $table->id('jawaban_id'); // PK
            
            // Foreign Keys
            $table->unsignedBigInteger('id_usaha');
            $table->unsignedBigInteger('pertanyaan_id');
            
            $table->foreign('id_usaha')->references('id_usaha')->on('usaha')->onDelete('cascade');
            $table->foreign('pertanyaan_id')->references('pertanyaan_id')->on('pertanyaan')->onDelete('cascade');
            
            $table->text('jawaban');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban');
    }
};
