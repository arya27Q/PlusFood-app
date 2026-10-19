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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('food_id')->constrained('foods')->cascadeOnDelete();
            $table->foreignId('merchant_id')->constrained('merchants')->cascadeOnDelete();
            
            $table->unsignedTinyInteger('rating'); // 1-5 bintang
            $table->text('comment')->nullable();
            $table->json('tags')->nullable(); // ["Porsi Banyak", "Masih Hangat", "Higienis", "Pelayanan Ramah"]
            $table->boolean('is_anonymous')->default(false);
            $table->unsignedInteger('reward_points')->default(20); // Reward poin gamifikasi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
