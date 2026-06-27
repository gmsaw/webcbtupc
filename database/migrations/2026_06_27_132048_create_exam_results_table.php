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
    Schema::create('exam_results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
        $table->decimal('score', 8, 2)->nullable(); // Nilai akhir
        $table->dateTime('start_time')->nullable(); // Kapan ujian dimulai
        $table->dateTime('end_time')->nullable(); // Kapan disubmit
        $table->integer('violation_count')->default(0); // Perekam pelanggaran (anti-cheat)
        $table->string('status')->default('not_started'); // not_started, in_progress, finished, disqualified
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_results');
    }
};
