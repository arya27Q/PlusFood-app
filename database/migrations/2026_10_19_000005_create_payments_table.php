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
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            
            // Metode Pembayaran & Gateway Sandbox Simulator
            $table->enum('payment_method', ['kasir', 'qris', 'gopay', 'dana'])->default('kasir');
            $table->string('payment_gateway')->default('sandbox_simulator'); // sandbox_simulator / cash_on_pickup
            $table->string('payment_reference')->unique()->nullable(); // e.g. SBX-QRIS-PF84920
            $table->unsignedInteger('amount'); // Nominal rupiah total tagihan yang dibayar
            $table->enum('status', ['menunggu_bayar', 'lunas', 'kadaluarsa', 'batal'])->default('lunas')->index();
            $table->timestamp('paid_at')->nullable();
            
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
