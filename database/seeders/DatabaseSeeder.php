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

        $merchant = \App\Models\Merchant::first();
        $food = \App\Models\Food::where('merchant_id', $merchant?->id)->first();

        if ($merchant && $food && \App\Models\Order::count() === 0) {
            $order1 = \App\Models\Order::create([
                'order_number' => 'PF-88219',
                'user_id' => $raka->id,
                'merchant_id' => $merchant->id,
                'total_items_price' => 12000,
                'total_packaging_fee' => 0,
                'service_fee' => 1000,
                'total_price' => 13000,
                'money_saved' => 13000,
                'weight_saved_grams' => 450,
                'customer_name' => 'Raka Pratama (Mhs. FT)',
                'customer_phone' => '081234567890',
                'customer_email' => 'raka@mhs.unesa.ac.id',
                'customer_nim' => '23051204117',
                'notes' => 'Tidak pakai sambal terlalu banyak ya bu.',
                'pickup_time' => '19.30 – 20.30 WIB',
                'pickup_deadline' => 'Hari ini tutup 21.00 WIB',
                'arrival_estimate' => 'tengah',
                'status' => 'menunggu',
                'qr_token' => 'PF-88219-BS',
            ]);

            \App\Models\OrderItem::create([
                'order_id' => $order1->id,
                'food_id' => $food->id,
                'food_name' => $food->name,
                'price' => $food->price_discount,
                'quantity' => 1,
                'subtotal' => $food->price_discount,
                'eco_packaging' => 'bawa_sendiri',
                'packaging_fee' => 0,
                'weight_grams' => 450,
            ]);

            \App\Models\Payment::create([
                'order_id' => $order1->id,
                'payment_method' => 'kasir',
                'payment_gateway' => 'cash_on_pickup',
                'payment_reference' => 'SBX-KASIR-PF-88219',
                'amount' => 13000,
                'status' => 'menunggu_bayar',
            ]);

            $order2 = \App\Models\Order::create([
                'order_number' => 'PF-84920',
                'user_id' => $raka->id,
                'merchant_id' => $merchant->id,
                'total_items_price' => 24000,
                'total_packaging_fee' => 1000,
                'service_fee' => 1000,
                'total_price' => 26000,
                'money_saved' => 26000,
                'weight_saved_grams' => 900,
                'customer_name' => 'Dimas Arya (Mhs. FBS)',
                'customer_phone' => '081234567891',
                'customer_email' => 'dimas.arya@mhs.unesa.ac.id',
                'customer_nim' => '23051204099',
                'notes' => 'Porsi dibungkus rapi, terima kasih bu.',
                'pickup_time' => '18.00 – 19.30 WIB',
                'pickup_deadline' => 'Hari ini tutup 21.00 WIB',
                'arrival_estimate' => 'awal',
                'status' => 'selesai',
                'qr_token' => 'PF-84920-BS',
                'completed_at' => now()->subMinutes(25),
            ]);

            \App\Models\OrderItem::create([
                'order_id' => $order2->id,
                'food_id' => $food->id,
                'food_name' => $food->name,
                'price' => $food->price_discount,
                'quantity' => 2,
                'subtotal' => 24000,
                'eco_packaging' => 'kotak_kertas',
                'packaging_fee' => 1000,
                'weight_grams' => 900,
            ]);

            \App\Models\Payment::create([
                'order_id' => $order2->id,
                'payment_method' => 'qris',
                'payment_gateway' => 'sandbox_simulator',
                'payment_reference' => 'SBX-QRIS-PF-84920',
                'amount' => 26000,
                'status' => 'lunas',
                'paid_at' => now()->subMinutes(25),
            ]);
        }
    }
}
