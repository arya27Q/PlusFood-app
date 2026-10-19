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
        Schema::create('merchant_daily_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->date('metric_date')->index();
            $table->unsignedSmallInteger('portions_listed')->default(0); // Kuota porsi surplus yang diunggah
            $table->unsignedSmallInteger('portions_rescued')->default(0); // Porsi yang laku terjual
            $table->decimal('recovery_rate', 5, 2)->default(0.00); // Persentase selamat (misal 75.00%)
            $table->unsignedBigInteger('revenue_rp')->default(0); // Pendapatan modal terselamatkan (Rp)
            $table->unsignedInteger('co2_prevented_grams')->default(0); // Gram emisi yang dicegah
            $table->timestamps();

            $table->unique(['merchant_id', 'metric_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_daily_metrics');
    }
};
