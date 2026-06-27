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
        Schema::table('competitions', function (Blueprint $table) {
            $table->decimal('nilai_benar', 5, 2)->default(1)->after('durasi_menit');
            $table->decimal('nilai_salah', 5, 2)->default(0)->after('nilai_benar');
            $table->decimal('nilai_kosong', 5, 2)->default(0)->after('nilai_salah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn(['nilai_benar', 'nilai_salah', 'nilai_kosong']);
        });
    }
};
