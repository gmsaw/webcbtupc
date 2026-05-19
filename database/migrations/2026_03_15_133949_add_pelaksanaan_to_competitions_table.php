<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dateTime('waktu_pelaksanaan')->nullable()->after('tanggal_selesai');
            $table->integer('durasi_menit')->default(120)->after('waktu_pelaksanaan'); // Default durasi 120 menit
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn(['waktu_pelaksanaan', 'durasi_menit']);
        });
    }
};