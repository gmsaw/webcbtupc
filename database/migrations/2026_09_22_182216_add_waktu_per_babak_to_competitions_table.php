<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dateTime('waktu_pelaksanaan_semifinal')->nullable()->after('waktu_pelaksanaan');
            $table->dateTime('waktu_pelaksanaan_final')->nullable()->after('waktu_pelaksanaan_semifinal');
        });
    }

    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropColumn(['waktu_pelaksanaan_semifinal', 'waktu_pelaksanaan_final']);
        });
    }
};