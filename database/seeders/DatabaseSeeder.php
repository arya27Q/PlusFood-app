<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $raka = User::firstOrCreate(
            ['email' => 'raka@mhs.unesa.ac.id'],
            [
                'name' => 'Raka Pratama',
                'password' => \Illuminate\Support\Facades\Hash::make('password123'),
                'role' => 'mahasiswa',
                'phone' => '081234567890',
                'campus' => 'Universitas Negeri Surabaya',
                'major' => 'S1 Teknik Informatika · 2023',
                'nim' => '23051204117',
                'address' => 'Jl. Ketintang Madya VI No. 21',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80',
                'co2_saved_grams' => 3400,
                'money_saved_rp' => 85000,
                'portions_rescued' => 5,
                'rescuer_level' => 2,
            ]
        );

        \App\Models\UserPreference::firstOrCreate(
            ['user_id' => $raka->id],
            [
                'campus_zone' => 'unesa-ketintang',
                'radius' => '2 km',
                'prefer_halal' => true,
                'prefer_vegetarian' => false,
                'prefer_nut_free' => false,
                'prefer_not_spicy' => true,
            ]
        );

        $this->call(FoodSeeder::class);
    }
}
