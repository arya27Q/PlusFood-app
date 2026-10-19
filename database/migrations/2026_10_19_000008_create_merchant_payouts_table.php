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
        Schema::create('merchant_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->string('reference_no')->unique(); // e.g. WD-20261019-8921
            $table->unsignedInteger('amount'); // Nominal penarikan dana (Rp)
            $table->unsignedInteger('fee')->default(0); // Biaya transfer antar-bank jika ada
            $table->unsignedInteger('net_amount'); // Jumlah bersih yang ditransfer
            
            // Rekening Tujuan
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('account_holder');
            
            $table->enum('status', ['menunggu', 'diproses', 'berhasil', 'gagal'])->default('diproses')->index();
            $table->text('notes')->nullable();
            $table->timestamp('transferred_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_payouts');
    }
};
