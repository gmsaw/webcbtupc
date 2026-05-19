<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            // Relasi ke tabel users dan competitions
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('competition_id')->constrained()->cascadeOnDelete();
            
            // Status Khusus Pendaftaran Lomba
            $table->enum('status_pendaftaran', ['pending', 'verified', 'rejected'])->default('pending');
            $table->enum('status_pembayaran', ['unpaid', 'paid'])->default('unpaid');
            
            // Rekam Jejak Ujian & Juara
            $table->decimal('nilai_cbt', 5, 2)->nullable(); // Kosong saat belum ujian
            $table->boolean('is_winner')->default(false);
            $table->string('peringkat')->nullable(); // Contoh: "Juara 1", "Juara Harapan 1"
            
            $table->timestamps();
            
            // Mencegah 1 user mendaftar lomba yang sama 2 kali
            $table->unique(['user_id', 'competition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};