<?php

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Pindahkan email dan password dari user mitra ke tabel merchants
        $mitraUsers = DB::table('users')->where('role', 'mitra')->get();

        foreach ($mitraUsers as $user) {
            $merchant = DB::table('merchants')->where('user_id', $user->id)->first();
            if ($merchant) {
                DB::table('merchants')->where('id', $merchant->id)->update([
                    'email' => $user->email,
                    'password' => $user->password,
                    'user_id' => null,
                ]);
            }
            // Hapus akun mitra dari tabel users (agar users 100% khusus akun Mahasiswa)
            DB::table('users')->where('id', $user->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
