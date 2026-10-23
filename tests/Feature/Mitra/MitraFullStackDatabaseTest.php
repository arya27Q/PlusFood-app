<?php

namespace Tests\Feature\Mitra;

use App\Models\Food;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class MitraFullStackDatabaseTest extends TestCase
{
    use RefreshDatabase;

    private User $mahasiswa;
    private Merchant $merchant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mahasiswa = User::factory()->create([
            'name' => 'Dimas Arya',
            'email' => 'dimas.arya@mhs.unesa.ac.id',
            'role' => 'mahasiswa',
            'phone' => '081234567891',
            'campus' => 'UNESA Ketintang',
            'nim' => '23051204099',
            'address' => 'Jl. Ketintang Madya No. 5',
        ]);

        $mitraUser = User::factory()->create([
            'name' => 'Ibu Sari',
            'email' => 'warung.busari@gmail.com',
            'role' => 'mitra',
        ]);

        $this->merchant = Merchant::create([
            'user_id' => $mitraUser->id,
            'name' => 'Warung Bu Sari',
            'owner_name' => 'Ibu Sari',
            'slug' => 'warung-bu-sari',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Warung Makan',
            'address' => 'Jl. Ketintang Baru No. 12, Surabaya',
            'canteen_location' => 'Kantin Vokasi Lt. 1 Stand 04',
            'phone' => '081234567890',
            'rating' => 4.8,
            'reviews_count' => 148,
            'is_verified' => true,
            'balance_rp' => 216000,
            'total_revenue_rp' => 1450000,
        ]);
    }

    /**
     * 1. Ketika UMKM menambah menu baru di /mitra/kelola-menu,
     * data harus tersimpan ke tabel foods MySQL, dan OTOMATIS langsung muncul di Beranda & Katalog Mahasiswa.
     */
    public function test_mitra_can_add_menu_and_it_automatically_appears_in_beranda_and_katalog(): void
    {
        $payload = [
            'name' => 'Nasi Rendang Sapi Spesial',
            'category' => 'Nasi',
            'price_original' => 30000,
            'price_discount' => 15000,
            'estimated_cost' => 10000,
            'stock' => 5,
            'canteen_location' => 'Kantin Vokasi Stand 04',
            'pickup_time_start' => '19.00',
            'pickup_time_end' => '20.30',
            'tags' => ['Halal', 'Bebas Kacang'],
            'spiciness' => 'Pedas Sedang',
            'image' => 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c',
            'description' => 'Rendang empuk bumbu rempah melimpah disajikan hangat.',
        ];

        // Mitra menambah menu via POST /mitra/kelola-menu
        $response = $this->post(route('mitra.kelola-menu.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Pastikan data tersimpan di tabel foods MySQL
        $this->assertDatabaseHas('foods', [
            'merchant_id' => $this->merchant->id,
            'name' => 'Nasi Rendang Sapi Spesial',
            'category' => 'Nasi',
            'price_original' => 30000,
            'price_discount' => 15000,
            'discount_percent' => 50,
            'stock' => 5,
            'status' => 'aktif',
        ]);

        $createdFood = Food::where('name', 'Nasi Rendang Sapi Spesial')->first();
        $this->assertNotNull($createdFood);

        // Uji halaman Beranda Mahasiswa: menu otomatis muncul
        $berandaResponse = $this->get(route('beranda'));
        $berandaResponse->assertOk();
        $berandaResponse->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Beranda')
            ->has('foods', fn (Assert $foods) => $foods
                ->where('0.name', 'Nasi Rendang Sapi Spesial')
                ->etc()
            )
        );

        // Uji halaman Katalog Mahasiswa: menu otomatis muncul
        $katalogResponse = $this->get(route('katalog'));
        $katalogResponse->assertOk();
        $katalogResponse->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Katalog')
            ->has('foods', fn (Assert $foods) => $foods
                ->where('0.name', 'Nasi Rendang Sapi Spesial')
                ->etc()
            )
        );
    }

    /**
     * Uji pembaruan stok dan toggle status menu oleh Mitra.
     */
    public function test_mitra_can_update_stock_and_toggle_status(): void
    {
        $food = Food::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Sop Buntut Gurih',
            'slug' => 'sop-buntut-gurih',
            'category' => 'Soto',
            'price_original' => 35000,
            'price_discount' => 18000,
            'discount_percent' => 49,
            'stock' => 4,
            'stock_initial' => 4,
            'status' => 'aktif',
        ]);

        // 1. Update stok menjadi 0 -> status otomatis 'habis'
        $updateStockResponse = $this->patch(route('mitra.kelola-menu.stock', $food->id), [
            'stock' => 0,
        ]);
        $updateStockResponse->assertRedirect();

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'stock' => 0,
            'status' => 'habis',
        ]);

        // 2. Toggle status kembali ke 'aktif'
        $toggleResponse = $this->patch(route('mitra.kelola-menu.toggle', $food->id));
        $toggleResponse->assertRedirect();

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'status' => 'aktif',
        ]);
    }

    /**
     * 2. Ketika Mahasiswa checkout di /checkout,
     * pesanan masuk ke tabel orders MySQL, dan OTOMATIS langsung muncul di Dashboard & Antrean Kasir Mitra.
     */
    public function test_mahasiswa_checkout_order_appears_in_mitra_dashboard_and_pesanan(): void
    {
        $food = Food::create([
            'merchant_id' => $this->merchant->id,
            'name' => 'Nasi Ayam Geprek Sambal Korek',
            'slug' => 'nasi-ayam-geprek',
            'category' => 'Nasi',
            'price_original' => 20000,
            'price_discount' => 10000,
            'discount_percent' => 50,
            'stock' => 5,
            'stock_initial' => 5,
            'pickup_time_start' => '19.00',
            'pickup_time_end' => '20.30',
            'pickup_time_display' => '19.00 – 20.30 WIB',
            'status' => 'aktif',
        ]);

        // Mahasiswa checkout di /checkout
        $checkoutResponse = $this->actingAs($this->mahasiswa)->post(route('checkout.store'), [
            'food_id' => $food->id,
            'quantity' => 2,
            'eco_packaging' => 'bawa_sendiri',
            'customer_name' => 'Dimas Arya',
            'customer_phone' => '081234567891',
            'customer_email' => 'dimas.arya@mhs.unesa.ac.id',
            'customer_address' => 'Jl. Ketintang Madya No. 5',
            'customer_nim' => '23051204099',
            'payment_method' => 'kasir',
            'arrival_estimate' => 'tengah',
            'remind_before' => true,
        ]);

        $checkoutResponse->assertRedirect();

        // Verifikasi tabel orders dan order_items di database MySQL
        $this->assertDatabaseHas('orders', [
            'merchant_id' => $this->merchant->id,
            'customer_name' => 'Dimas Arya',
            'total_items_price' => 20000,
            'status' => 'menunggu',
        ]);

        $order = Order::where('merchant_id', $this->merchant->id)->first();
        $this->assertNotNull($order);

        // Uji Dashboard Mitra: pesanan mahasiswa otomatis tampil dari database MySQL
        $dashboardResponse = $this->get(route('mitra.dashboard'));
        $dashboardResponse->assertOk();
        $dashboardResponse->assertInertia(fn (Assert $page) => $page
            ->component('Mitra/Dashboard')
            ->has('orders', 1)
            ->where('orders.0.customer_name', 'Dimas Arya')
            ->where('orders.0.status', 'menunggu')
        );

        // Uji Halaman Pesanan Mitra: pesanan mahasiswa otomatis tampil di antrean kasir
        $pesananResponse = $this->get(route('mitra.pesanan'));
        $pesananResponse->assertOk();
        $pesananResponse->assertInertia(fn (Assert $page) => $page
            ->component('Mitra/Pesanan')
            ->has('orders', 1)
            ->where('orders.0.customer_name', 'Dimas Arya')
            ->where('orders.0.status', 'menunggu')
        );
    }

    /**
     * 3. Ketika Mitra memvalidasi tiket / scan QR di Dashboard,
     * status pesanan di tabel orders MySQL berubah menjadi 'selesai'.
     */
    public function test_mitra_validates_order_and_status_changes_to_selesai_in_mysql(): void
    {
        $order = Order::create([
            'order_number' => 'PF-77291',
            'user_id' => $this->mahasiswa->id,
            'merchant_id' => $this->merchant->id,
            'total_items_price' => 12000,
            'total_packaging_fee' => 0,
            'service_fee' => 1000,
            'total_price' => 13000,
            'money_saved' => 13000,
            'weight_saved_grams' => 400,
            'customer_name' => 'Dimas Arya',
            'customer_phone' => '081234567891',
            'customer_email' => 'dimas.arya@mhs.unesa.ac.id',
            'customer_nim' => '23051204099',
            'pickup_time' => '19.00 – 20.30 WIB',
            'status' => 'menunggu',
            'qr_token' => 'PF-77291-WS',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'kasir',
            'payment_gateway' => 'cash_on_pickup',
            'payment_reference' => 'SBX-KASIR-PF-77291',
            'amount' => 13000,
            'status' => 'menunggu_bayar',
        ]);

        // Mitra memvalidasi pesanan / scan QR menjadi 'selesai'
        $validateResponse = $this->patch(route('mitra.pesanan.status', $order->id), [
            'status' => 'selesai',
        ]);

        $validateResponse->assertRedirect();
        $validateResponse->assertSessionHas('success');

        // Status pesanan di database MySQL berubah menjadi 'selesai'
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'selesai',
        ]);

        $order->refresh();
        $this->assertNotNull($order->completed_at);

        // Status pembayaran kasir otomatis menjadi 'lunas'
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'status' => 'lunas',
        ]);
    }
}
