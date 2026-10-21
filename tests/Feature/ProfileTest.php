<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profil');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profil')
            ->put('/profil', [
                'name' => 'Raka Pratama Updated',
                'email' => 'raka.updated@mhs.unesa.ac.id',
                'phone' => '08987654321',
                'campus' => 'Universitas Negeri Surabaya',
                'major' => 'S1 Sistem Informasi · 2024',
                'nim' => '24051204099',
                'address' => 'Jl. Ketintang Baru XII',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profil');

        $user->refresh();

        $this->assertSame('Raka Pratama Updated', $user->name);
        $this->assertSame('raka.updated@mhs.unesa.ac.id', $user->email);
        $this->assertSame('08987654321', $user->phone);
        $this->assertSame('24051204099', $user->nim);
    }

    public function test_user_preferences_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profil')
            ->post('/profil/preferences', [
                'campus_zone' => 'its-sukolilo',
                'radius' => '3 km',
                'prefer_halal' => true,
                'prefer_vegetarian' => true,
                'prefer_nut_free' => false,
                'prefer_not_spicy' => false,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profil');

        $this->assertDatabaseHas('user_preferences', [
            'user_id' => $user->id,
            'campus_zone' => 'its-sukolilo',
            'radius' => '3 km',
            'prefer_vegetarian' => true,
        ]);
    }

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->actingAs($user)
            ->from('/profil')
            ->put('/profil/password', [
                'current_password' => 'password123',
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profil');

        $this->assertTrue(Hash::check('newpassword123', $user->refresh()->password));
    }
}
