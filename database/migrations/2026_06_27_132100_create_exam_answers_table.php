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
    Schema::create('exam_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
        $table->foreignId('question_id')->constrained()->cascadeOnDelete();
        $table->string('answer_selected')->nullable(); // A, B, C, D, E
        $table->boolean('is_doubtful')->default(false); // Penanda Ragu-ragu
        $table->boolean('is_correct')->nullable(); // Benar/Salah (diisi saat kalkulasi)
        $table->timestamps();

        // Mencegah 1 peserta menjawab soal yang sama 2 kali di database
        $table->unique(['registration_id', 'question_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
    }
};
