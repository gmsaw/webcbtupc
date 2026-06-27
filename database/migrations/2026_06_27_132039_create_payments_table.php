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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('registration_id')->constrained()->cascadeOnDelete();
        $table->string('order_id')->unique(); // Order ID unik untuk Midtrans
        $table->decimal('amount', 10, 2);
        $table->string('status')->default('unpaid'); // unpaid, paid, expired, failed
        $table->string('payment_type')->nullable(); // bank_transfer, qris, gopay
        $table->string('snap_token')->nullable(); // Token Midtrans
        $table->dateTime('paid_at')->nullable(); // Kapan lunas
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
