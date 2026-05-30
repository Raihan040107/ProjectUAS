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
        Schema::create('dampak', function (Blueprint $table) {
            $table->id('id_dampak'); // PK
            
            // Foreign Key
            $table->unsignedBigInteger('id_usaha');
            $table->foreign('id_usaha')->references('id_usaha')->on('usaha')->onDelete('cascade');
            
            $table->text('dampak_lingkungan');
            $table->text('dampak_sosial');
            $table->text('dampak_ekonomi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dampak');
    }
};
