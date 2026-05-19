<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchandises', function (Blueprint $table) {
            // Menandai apakah ini E-Book (true) atau Barang Fisik (false)
            $table->boolean('is_digital')->default(false)->after('link_pembelian');
        });
    }

    public function down(): void
    {
        Schema::table('merchandises', function (Blueprint $table) {
            $table->dropColumn('is_digital');
        });
    }
};