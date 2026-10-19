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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['mahasiswa', 'mitra', 'admin'])->default('mahasiswa')->after('password');
            $table->string('phone')->nullable()->after('role');
            $table->string('campus')->nullable()->after('phone');
            $table->string('nim')->nullable()->after('campus');
            $table->string('avatar')->nullable()->after('nim');
            
            // Gamifikasi & Impact Tracker Penyelamatan Makanan
            $table->unsignedInteger('co2_saved_grams')->default(0)->after('avatar');
            $table->unsignedBigInteger('money_saved_rp')->default(0)->after('co2_saved_grams');
            $table->unsignedInteger('portions_rescued')->default(0)->after('money_saved_rp');
            $table->unsignedTinyInteger('rescuer_level')->default(1)->after('portions_rescued');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role',
                'phone',
                'campus',
                'nim',
                'avatar',
                'co2_saved_grams',
                'money_saved_rp',
                'portions_rescued',
                'rescuer_level',
            ]);
        });
    }
};
