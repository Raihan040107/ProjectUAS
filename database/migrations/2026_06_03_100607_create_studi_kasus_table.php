// database/migrations/xxxx_create_studi_kasus_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('studi_kasus', function (Blueprint $table) {
            $table->id();
            $table->string('nomor', 10);           // "01", "02", dst
            $table->string('nama_usaha', 255);
            $table->text('deskripsi');
            $table->json('pencapaian');             // array string pencapaian
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('studi_kasus');
    }
};
