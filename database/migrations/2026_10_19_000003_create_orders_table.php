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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // e.g. PF-84920
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            
            // Item & Biaya
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->unsignedInteger('subtotal');
            $table->enum('eco_packaging', ['bawa_sendiri', 'kotak_kertas', 'thinwall'])->default('bawa_sendiri');
            $table->unsignedInteger('packaging_fee')->default(0);
            $table->unsignedInteger('service_fee')->default(1000);
            $table->unsignedInteger('total_price');
            
            // Dampak Penghematan & Lingkungan
            $table->unsignedInteger('money_saved');
            $table->unsignedInteger('weight_saved_grams')->default(400);
            
            // Data Pemesan Mahasiswa (Snapshot form)
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email');
            $table->string('customer_address')->nullable();
            $table->string('customer_nim')->nullable(); // Opsional / bebas tanpa NIM
            $table->text('notes')->nullable(); // Alergi / instruksi
            
            // Pickup & Kedatangan
            $table->string('pickup_time');
            $table->string('pickup_deadline')->nullable();
            $table->enum('arrival_estimate', ['awal', 'tengah', 'akhir'])->default('tengah');
            $table->boolean('remind_before')->default(true);
            
            // Status Alur Penjemputan
            $table->enum('status', ['menunggu', 'siap_ambil', 'selesai', 'batal'])->default('menunggu')->index();
            
            // Token QR Digital 3-digit / string
            $table->string('qr_token')->unique(); // e.g. PF-84920-WS
            $table->boolean('has_review')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
