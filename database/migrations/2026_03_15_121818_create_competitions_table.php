<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competitions', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lomba'); // Contoh: "Olimpiade Fisika SMA", "Cerdas Cermat"
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_pendaftaran', 10, 2)->default(0); // Untuk integrasi Midtrans nanti
            $table->boolean('is_active')->default(true); // Status apakah pendaftaran masih buka
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competitions');
    }
};
