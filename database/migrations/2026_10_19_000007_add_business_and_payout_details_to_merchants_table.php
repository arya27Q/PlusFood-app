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
        Schema::table('merchants', function (Blueprint $table) {
            $table->string('owner_name')->nullable()->after('name');
            $table->string('nib')->nullable()->after('owner_name'); // Nomor Induk Berusaha UMKM
            $table->string('canteen_location')->nullable()->after('address'); // Contoh: Kantin Vokasi Lt. 1 Stand 04
            $table->enum('default_session', ['siang', 'sore', 'malam'])->default('malam')->after('operating_hours');
            $table->boolean('support_eco')->default(true)->after('default_session'); // Dukung Bawa Wadah Sendiri
            
            // Rekening Pencairan Dana Mitra (Payout)
            $table->string('bank_name')->nullable()->after('support_eco'); // e.g. Bank Mandiri, BCA, BRI
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->string('bank_holder')->nullable()->after('bank_account');
            
            // Saldo Dompet & Akumulasi Omzet Mitra
            $table->unsignedBigInteger('balance_rp')->default(0)->after('bank_holder'); // Saldo siap ditarik
            $table->unsignedBigInteger('total_revenue_rp')->default(0)->after('balance_rp'); // Total omzet penjualan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('merchants', function (Blueprint $table) {
            $table->dropColumn([
                'owner_name',
                'nib',
                'canteen_location',
                'default_session',
                'support_eco',
                'bank_name',
                'bank_account',
                'bank_holder',
                'balance_rp',
                'total_revenue_rp',
            ]);
        });
    }
};
