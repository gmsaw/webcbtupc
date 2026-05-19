<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchandise_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('merchandise_id')->constrained()->cascadeOnDelete();
            $table->decimal('nominal', 12, 2); // Harga saat dibeli
            $table->string('metode_pembayaran')->default('manual');
            $table->enum('status', ['pending', 'paid', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchandise_transactions');
    }
};