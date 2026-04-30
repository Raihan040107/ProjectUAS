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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id('id_dokumen'); // PK
            
            // Foreign Key
            $table->unsignedBigInteger('id_usaha');
            $table->foreign('id_usaha')->references('id_usaha')->on('usaha')->onDelete('cascade');
            
            $table->string('ktp');
            $table->string('npwp');
            $table->string('surat_izin_usaha');
            $table->string('status_verifikasi');
            $table->date('tanggal_registrasi');
            $table->date('tanggal_verifikasi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumens');
    }
};
