<?php

namespace Tests\Feature\Mahasiswa;

use App\Models\Food;
use App\Models\Merchant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class KatalogMakananTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_beranda_page_can_be_rendered_with_active_foods(): void
    {
        $response = $this->get(route('beranda'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Beranda')
            ->has('foods')
        );
    }

    public function test_katalog_page_can_be_rendered_with_foods_and_filters(): void
    {
        $response = $this->get(route('katalog'));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Katalog')
            ->has('foods')
            ->has('filters')
        );
    }

    public function test_katalog_can_be_filtered_by_category(): void
    {
        $response = $this->get(route('katalog', ['category' => 'Nasi']));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/Katalog')
            ->where('filters.category', 'Nasi')
            ->has('foods')
        );
    }

    public function test_detail_produk_page_can_be_rendered_from_database(): void
    {
        $food = Food::first();
        $this->assertNotNull($food);

        $response = $this->get(route('makanan.detail', ['slug' => $food->slug]));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Mahasiswa/DetailProduk')
            ->where('slug', $food->slug)
            ->has('food')
        );
    }

    public function test_food_belongs_to_merchant_and_has_accessors(): void
    {
        $food = Food::first();

        $this->assertInstanceOf(Merchant::class, $food->merchant);
        $this->assertNotEmpty($food->merchant->initial);
        $this->assertIsInt($food->priceDiscount);
        $this->assertIsInt($food->priceOriginal);
        $this->assertNotEmpty($food->co2Saved);
    }
}
