<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('competitions', function (Blueprint $table) {
        // Tambahkan toggle apakah lomba pakai gelombang atau tidak
        $table->boolean('is_using_waves')->default(false);
    });
}

public function down(): void
{
    Schema::table('competitions', function (Blueprint $table) {
        $table->dropColumn('is_using_waves');
    });
}
};
