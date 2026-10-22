<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Food;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemesananCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Merchant $merchant;
    private Food $food;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Raka Pratama',
            'email' => 'raka.pratama@mhs.unesa.ac.id',
            'role' => 'mahasiswa',
            'phone' => '081234567890',
            'campus' => 'Universitas Negeri Surabaya',
            'nim' => '23051204088',
            'address' => 'Jl. Ketintang Baru No. 14, Gayungan',
        ]);

        $merchantOwner = User::factory()->create([
            'name' => 'Pemilik Warung',
            'email' => 'pemilik.warung@gmail.com',
            'role' => 'mitra',
        ]);

        $this->merchant = Merchant::create([
            'user_id' => $merchantOwner->id,
            'name' => 'Warung Nasi Sedap',
            'slug' => 'warung-nasi-sedap',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'Universitas Negeri Surabaya',
            'address' => 'Jl. Ketintang No. 10',
            'is_verified' => true,
        ]);

        $this->food = Food::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Nasi Ayam Bakar Madu',
            'slug' => 'nasi-ayam-bakar-madu',
            'category' => 'Nasi',
            'description' => 'Ayam bakar lezat hangat.',
            'price_original' => 25000,
            'price_discount' => 12000,
            'discount_percent' => 52,
            'stock' => 5,
            'stock_initial' => 5,
            'weight_grams' => 450,
            'pickup_time_start' => '19:30',
            'pickup_time_end' => '20:30',
            'pickup_time_display' => '19.30 – 20.30 WIB',
            'pickup_deadline_info' => 'Hari ini',
            'dietary_tags' => ['Halal', 'Tidak Pedas'],
            'is_sealed' => true,
            'is_cooked_today' => true,
            'status' => 'aktif',
        ]);
    }

    public function test_halaman_detail_makanan_dapat_diakses_berdasarkan_slug(): void
    {
        $response = $this->get('/makanan/' . $this->food->slug);

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/DetailProduk')
            ->has('food')
            ->where('food.name', 'Nasi Ayam Bakar Madu')
        );
    }

    public function test_halaman_checkout_dapat_diakses_dengan_parameter_query(): void
    {
        $response = $this->actingAs($this->user)
            ->get('/checkout?slug=' . $this->food->slug . '&qty=2&packaging=kotak_kertas');

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Mahasiswa/Checkout')
            ->has('food')
            ->where('initialQuantity', 2)
            ->where('initialPackaging', 'kotak_kertas')
            ->where('customer.name', 'Raka Pratama')
        );
    }

    public function test_berhasil_membuat_pesanan_dan_transaksi_sandbox_gateway(): void
    {
        $initialStock = $this->food->stock;

        $payload = [
            'food_id' => $this->food->id,
            'quantity' => 2,
            'eco_packaging' => 'kotak_kertas', // + Rp1.000
            'customer_name' => 'Raka Pratama',
            'customer_phone' => '081234567890',
            'customer_email' => 'raka.pratama@mhs.unesa.ac.id',
            'customer_address' => 'Jl. Ketintang Baru No. 14, Gayungan',
            'customer_nim' => '23051204088',
            'notes' => 'Tolong sambal dipisah',
            'payment_method' => 'qris',
            'arrival_estimate' => 'tengah',
            'remind_before' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post('/checkout', $payload);

        // Verifikasi redirect ke halaman tiket
        $response->assertRedirect();
        $this->assertStringContainsString('/tiket', $response->headers->get('Location'));

        // 1. Verifikasi header pesanan di database orders
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'merchant_id' => $this->merchant->id,
            'total_items_price' => 24000, // 2 * 12.000
            'total_packaging_fee' => 1000,
            'service_fee' => 1000,
            'total_price' => 26000, // 24.000 + 1.000 + 1.000
            'status' => 'menunggu',
        ]);

        // 2. Verifikasi order item di database order_items
        $this->assertDatabaseHas('order_items', [
            'food_id' => $this->food->id,
            'food_name' => 'Nasi Ayam Bakar Madu',
            'quantity' => 2,
            'subtotal' => 24000,
            'eco_packaging' => 'kotak_kertas',
        ]);

        // 3. Verifikasi pembayaran gateway sandbox lunas di database payments
        $this->assertDatabaseHas('payments', [
            'payment_method' => 'qris',
            'payment_gateway' => 'sandbox_simulator',
            'status' => 'lunas',
            'amount' => 26000,
        ]);

        // 4. Verifikasi pengurangan stok makanan
        $this->assertEquals($initialStock - 2, $this->food->fresh()->stock);

        // 5. Verifikasi saldo mitra bertambah
        $this->assertEquals(25000, $this->merchant->fresh()->balance_rp); // 24.000 item + 1.000 kemasan
    }

    public function test_pembayaran_di_kasir_memiliki_status_menunggu_bayar(): void
    {
        $payload = [
            'food_id' => $this->food->id,
            'quantity' => 1,
            'eco_packaging' => 'bawa_sendiri',
            'customer_name' => 'Raka Pratama',
            'customer_phone' => '081234567890',
            'customer_email' => 'raka.pratama@mhs.unesa.ac.id',
            'customer_address' => 'Jl. Ketintang Baru No. 14, Gayungan',
            'customer_nim' => '23051204088',
            'payment_method' => 'kasir',
            'arrival_estimate' => 'awal',
            'remind_before' => true,
        ];

        $response = $this->actingAs($this->user)
            ->post('/checkout', $payload);

        $response->assertRedirect();

        $this->assertDatabaseHas('payments', [
            'payment_method' => 'kasir',
            'payment_gateway' => 'cash_on_pickup',
            'status' => 'menunggu_bayar',
        ]);
    }

    public function test_gagal_checkout_jika_jumlah_melebihi_stok(): void
    {
        $payload = [
            'food_id' => $this->food->id,
            'quantity' => 999, // Melebihi stok 5
            'eco_packaging' => 'bawa_sendiri',
            'customer_name' => 'Raka Pratama',
            'customer_phone' => '081234567890',
            'customer_email' => 'raka.pratama@mhs.unesa.ac.id',
            'customer_address' => 'Jl. Ketintang',
            'payment_method' => 'qris',
            'arrival_estimate' => 'tengah',
        ];

        $response = $this->actingAs($this->user)
            ->from('/checkout')
            ->post('/checkout', $payload);

        $response->assertRedirect('/checkout');
        $response->assertSessionHasErrors(['quantity']);

        // Stok tidak berubah
        $this->assertEquals(5, $this->food->fresh()->stock);
    }
}
