<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchandise_transactions', function (Blueprint $table) {
            $table->string('order_id')->nullable()->after('id');
            $table->string('snap_token')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('merchandise_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'snap_token']);
        });
    }
};