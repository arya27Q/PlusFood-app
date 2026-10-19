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
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('slug')->unique();
            $table->string('nib')->nullable(); // Nomor Induk Berusaha UMKM
            $table->string('campus_zone')->index(); // unesa-ketintang, unesa-lidah, unair-b, its-sukolilo
            $table->string('campus_name');
            $table->string('category')->default('Kantin'); // Warung Makan, Roti & Bakery, Kedai Soto, dll
            $table->string('address');
            $table->string('canteen_location')->nullable(); // Kantin Vokasi Lt. 1 Stand 04
            $table->string('phone')->nullable();
            $table->decimal('rating', 2, 1)->default(4.8);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('is_verified')->default(true);
            $table->string('operating_hours')->default('17.00 – 21.00 WIB');
            $table->enum('default_session', ['siang', 'sore', 'malam'])->default('malam');
            $table->boolean('support_eco')->default(true); // Dukung Bawa Wadah Sendiri
            
            // Rekening Pencairan Dana Mitra (Payout)
            $table->string('bank_name')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('bank_holder')->nullable();
            
            // Saldo Dompet & Akumulasi Omzet Mitra
            $table->unsignedBigInteger('balance_rp')->default(0);
            $table->unsignedBigInteger('total_revenue_rp')->default(0);
            
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
