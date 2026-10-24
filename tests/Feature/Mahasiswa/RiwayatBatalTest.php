<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Food;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RiwayatBatalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private Food $food;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@mhs.unesa.ac.id',
            'role' => 'mahasiswa',
        ]);

        $merchantOwner = User::factory()->create([
            'role' => 'mitra',
        ]);

        $this->merchant = Merchant::create([
            'user_id' => $merchantOwner->id,
            'name' => 'Warung Barokah',
            'slug' => 'warung-barokah',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'Universitas Negeri Surabaya',
            'address' => 'Jl. Ketintang No. 5',
            'is_verified' => true,
        ]);

        $this->food = Food::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Ayam Geprek Sambal Korek',
            'slug' => 'ayam-geprek-sambal-korek',
            'category' => 'Ayam',
            'description' => 'Ayam geprek renyah pedas.',
            'price_original' => 20000,
            'price_discount' => 10000,
            'discount_percent' => 50,
            'stock' => 2,
            'stock_initial' => 5,
            'weight_grams' => 350,
            'status' => 'aktif',
        ]);
    }

    public function test_pesanan_dapat_dibatalkan_dalam_toleransi_15_menit_dengan_alasan(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-20261024-001',
            'user_id' => $this->user->id,
            'merchant_id' => $this->merchant->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi.santoso@mhs.unesa.ac.id',
            'total_items_price' => 20000,
            'total_packaging_fee' => 0,
            'service_fee' => 1000,
            'total_price' => 21000,
            'pickup_time' => '19.00 – 20.30 WIB',
            'qr_token' => 'QR-12345',
            'money_saved' => 20000,
            'weight_saved_grams' => 700,
            'status' => 'menunggu',
            'notes' => 'Catatan awal',
            'created_at' => now()->subMinutes(5), // 5 menit yang lalu (<= 15 menit)
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $this->food->id,
            'food_name' => $this->food->name,
            'quantity' => 2,
            'price' => 10000,
            'subtotal' => 20000,
            'eco_packaging' => 'bawa_sendiri',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('riwayat.batal', $order->id), [
                'reason' => 'Salah pilih menu atau jumlah porsi',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $freshOrder = $order->fresh();
        $this->assertEquals('batal', $freshOrder->status);
        $this->assertStringContainsString('Alasan batal: Salah pilih menu atau jumlah porsi', $freshOrder->notes);

        // Stok makanan dikembalikan (+2 dari 2 menjadi 4)
        $this->assertEquals(4, $this->food->fresh()->stock);
    }

    public function test_pesanan_lebih_dari_15_menit_tidak_dapat_dibatalkan(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-20261024-002',
            'user_id' => $this->user->id,
            'merchant_id' => $this->merchant->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi.santoso@mhs.unesa.ac.id',
            'total_items_price' => 10000,
            'total_packaging_fee' => 0,
            'service_fee' => 1000,
            'total_price' => 11000,
            'pickup_time' => '19.00 – 20.30 WIB',
            'qr_token' => 'QR-12345',
            'money_saved' => 10000,
            'weight_saved_grams' => 350,
            'status' => 'menunggu',
        ]);
        Order::where('id', $order->id)->update(['created_at' => now()->subMinutes(16)]);

        OrderItem::create([
            'order_id' => $order->id,
            'food_id' => $this->food->id,
            'food_name' => $this->food->name,
            'quantity' => 1,
            'price' => 10000,
            'subtotal' => 10000,
            'eco_packaging' => 'bawa_sendiri',
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('riwayat.batal', $order->id), [
                'reason' => 'Jadwal kuliah mendadak',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pesanan tidak dapat dibatalkan karena sudah melewati batas waktu toleransi 15 menit.');

        // Status dan stok tidak berubah
        $this->assertEquals('menunggu', $order->fresh()->status);
        $this->assertEquals(2, $this->food->fresh()->stock);
    }

    public function test_pesanan_selesai_tidak_dapat_dibatalkan(): void
    {
        $order = Order::create([
            'order_number' => 'ORD-20261024-003',
            'user_id' => $this->user->id,
            'merchant_id' => $this->merchant->id,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
            'customer_email' => 'budi.santoso@mhs.unesa.ac.id',
            'total_items_price' => 10000,
            'total_packaging_fee' => 0,
            'service_fee' => 1000,
            'total_price' => 11000,
            'pickup_time' => '19.00 – 20.30 WIB',
            'qr_token' => 'QR-12345',
            'money_saved' => 10000,
            'weight_saved_grams' => 350,
            'status' => 'selesai',
            'created_at' => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($this->user)
            ->post(route('riwayat.batal', $order->id), [
                'reason' => 'Ingin batalkan',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Pesanan yang sudah selesai diambil tidak dapat dibatalkan.');
        $this->assertEquals('selesai', $order->fresh()->status);
    }
}
