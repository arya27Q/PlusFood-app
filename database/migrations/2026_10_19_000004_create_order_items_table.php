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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
            
            // Snapshot Data Item & Harga saat Transaksi
            $table->string('food_name'); // Snapshot nama menu makanan
            $table->unsignedInteger('price'); // Harga satuan surplus saat checkout (Rp)
            $table->unsignedSmallInteger('quantity')->default(1); // Jumlah porsi yang dipesan
            $table->unsignedInteger('subtotal'); // price * quantity
            
            // Pilihan Wadah Ramah Lingkungan per Item
            $table->enum('eco_packaging', ['bawa_sendiri', 'kotak_kertas', 'thinwall'])->default('bawa_sendiri');
            $table->unsignedInteger('packaging_fee')->default(0); // Biaya kemasan (Rp0 / Rp1.000 / Rp2.000)
            
            // Bobot Gram Makanan untuk Kalkulasi Dampak
            $table->unsignedSmallInteger('weight_grams')->default(400); // Estimasi berat porsi (gram)
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
