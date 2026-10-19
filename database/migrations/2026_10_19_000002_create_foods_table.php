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
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->index(); // Nasi, Mie, Soto, Roti, Camilan, Minuman
            $table->text('description')->nullable();
            
            // Harga & Diskon Surplus
            $table->unsignedInteger('price_original');
            $table->unsignedInteger('price_discount');
            $table->unsignedTinyInteger('discount_percent');
            
            // Stok & Porsi
            $table->unsignedSmallInteger('stock')->default(1);
            $table->unsignedSmallInteger('stock_initial')->default(1);
            $table->unsignedSmallInteger('weight_grams')->default(400); // estimasi berat porsi
            
            // Jadwal Ambil (Pickup)
            $table->string('pickup_time_start')->default('19:30');
            $table->string('pickup_time_end')->default('20:30');
            $table->string('pickup_time_display')->default('19.30 – 20.30 WIB');
            $table->string('pickup_deadline_info')->default('Hari ini sebelum tutup 21.00 WIB');
            
            // Tag Diet, Keamanan, & Kualitas
            $table->json('dietary_tags')->nullable(); // ["Halal", "Tidak Pedas", "Vegetarian", "Bebas Kacang"]
            $table->boolean('is_sealed')->default(true);
            $table->boolean('is_cooked_today')->default(true);
            $table->string('image')->nullable();
            
            $table->enum('status', ['aktif', 'habis', 'berakhir'])->default('aktif')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};
