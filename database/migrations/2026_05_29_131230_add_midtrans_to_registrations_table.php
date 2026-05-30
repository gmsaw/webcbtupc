<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Menambahkan kolom order_id dan snap_token
            $table->string('order_id')->nullable()->after('id');
            $table->string('snap_token')->nullable()->after('status_pendaftaran');
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Menghapus kolom jika migrasi di-rollback
            $table->dropColumn(['order_id', 'snap_token']);
        });
    }
};