<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('keunggulan', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 10);        // "01", "02", dst
            $table->string('judul', 255);
            $table->text('deskripsi');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('keunggulan');
    }
};
