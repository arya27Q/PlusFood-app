<?php

namespace Tests\Feature\Auth;

use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossRoleRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cannot_register_mitra_using_mahasiswa_email(): void
    {
        User::create([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi.mahasiswa@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->post('/mitra/register', [
            'storeName' => 'Toko Budi',
            'email' => 'budi.mahasiswa@gmail.com',
            'password' => 'secret123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(1, User::count());
        $this->assertEquals(0, Merchant::count());
    }

    public function test_cannot_register_mahasiswa_using_mitra_email(): void
    {
        Merchant::create([
            'name' => 'Warung Sari',
            'owner_name' => 'Warung Sari',
            'email' => 'warung.sari@gmail.com',
            'password' => bcrypt('password123'),
            'slug' => 'warung-sari',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Kantin Kampus',
            'address' => 'UNESA Ketintang',
        ]);

        $response = $this->post('/register', [
            'name' => 'Sari Mahasiswa',
            'email' => 'warung.sari@gmail.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertEquals(0, User::count());
    }

    public function test_mitra_user_cannot_login_through_mahasiswa_login(): void
    {
        Merchant::create([
            'name' => 'Warung Sari',
            'owner_name' => 'Warung Sari',
            'email' => 'warung.sari@gmail.com',
            'password' => bcrypt('password123'),
            'slug' => 'warung-sari',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Kantin Kampus',
            'address' => 'UNESA Ketintang',
        ]);

        $response = $this->post('/login', [
            'email' => 'warung.sari@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_mahasiswa_user_cannot_login_through_mitra_login(): void
    {
        User::create([
            'name' => 'Budi Mahasiswa',
            'email' => 'budi.mahasiswa@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->post('/mitra/login', [
            'email' => 'budi.mahasiswa@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_can_register_mitra_with_fresh_email_and_redirects_to_login(): void
    {
        $response = $this->post('/mitra/register', [
            'storeName' => 'Warung Baru Sukses',
            'email' => 'warung.baru@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('mitra.login'));
        $response->assertSessionHas('status');
        $this->assertGuest('mitra');

        // Pastikan TIDAK ADA di tabel users (murni di tabel merchants!)
        $this->assertNull(User::where('email', 'warung.baru@gmail.com')->first());

        $merchant = Merchant::where('email', 'warung.baru@gmail.com')->first();
        $this->assertNotNull($merchant);
        $this->assertEquals('Warung Baru Sukses', $merchant->name);
    }

    public function test_logged_in_mitra_sees_their_own_store_in_profile(): void
    {
        $merchant = Merchant::create([
            'name' => 'Toko Keren Abadi',
            'owner_name' => 'Pemilik Toko Keren',
            'email' => 'toko.keren@gmail.com',
            'password' => bcrypt('password123'),
            'slug' => 'toko-keren-abadi',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Kantin Kampus',
            'address' => 'UNESA Ketintang',
            'canteen_location' => 'Kantin Stand 10',
            'phone' => '089912345678',
            'rating' => 5.0,
            'reviews_count' => 0,
            'is_verified' => true,
            'operating_hours' => '17.00 - 21.00 WIB',
            'default_session' => 'malam',
            'support_eco' => true,
            'balance_rp' => 0,
            'total_revenue_rp' => 0,
        ]);

        $response = $this->actingAs($merchant, 'mitra')->get('/mitra/profil');
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Mitra/Profil')
            ->where('merchant.name', 'Toko Keren Abadi')
            ->where('user.email', 'toko.keren@gmail.com')
        );
    }

    public function test_mahasiswa_user_is_redirected_when_accessing_mitra_dashboard(): void
    {
        $mahasiswaUser = User::create([
            'name' => 'Yudhis Mahasiswa',
            'email' => 'yudhis.mhs@gmail.com',
            'password' => bcrypt('password123'),
            'role' => 'mahasiswa',
        ]);

        $response = $this->actingAs($mahasiswaUser)->get('/mitra/dashboard');
        $response->assertRedirect(route('mitra.login'));
    }

    public function test_deleting_merchant_automatically_deletes_their_foods(): void
    {
        $merchant = Merchant::create([
            'name' => 'Toko Budi Barokah',
            'owner_name' => 'Toko Budi',
            'email' => 'budi.hapus@gmail.com',
            'password' => bcrypt('password123'),
            'slug' => 'toko-budi-barokah',
            'campus_zone' => 'unesa-ketintang',
            'campus_name' => 'UNESA Ketintang',
            'category' => 'Kantin Kampus',
            'address' => 'UNESA Ketintang',
        ]);

        \App\Models\Food::create([
            'merchant_id' => $merchant->id,
            'name' => 'Nasi Goreng Budi',
            'slug' => 'nasi-goreng-budi',
            'category' => 'Nasi',
            'price_original' => 20000,
            'price_discount' => 10000,
            'discount_percent' => 50,
            'stock' => 5,
        ]);

        $this->assertDatabaseHas('merchants', ['id' => $merchant->id]);
        $this->assertDatabaseHas('foods', ['merchant_id' => $merchant->id]);

        $merchant->delete();

        $this->assertDatabaseMissing('merchants', ['id' => $merchant->id]);
        $this->assertDatabaseMissing('foods', ['merchant_id' => $merchant->id]);
    }
}

