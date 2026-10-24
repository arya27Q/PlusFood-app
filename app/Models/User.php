<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'campus',
        'major',
        'nim',
        'address',
        'avatar',
        'co2_saved_grams',
        'money_saved_rp',
        'portions_rescued',
        'rescuer_level',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'co2_saved_grams' => 'integer',
            'money_saved_rp' => 'integer',
            'portions_rescued' => 'integer',
            'rescuer_level' => 'integer',
        ];
    }

    /**
     * Bootstrap the model and its events.
     */
    protected static function booted(): void
    {
        static::deleting(function (User $user) {
            if ($user->merchant) {
                $user->merchant->foods()->delete();
                $user->merchant->delete();
            }
            if ($user->preference) {
                $user->preference->delete();
            }
        });
    }

    /**
     * User dietary and campus preferences (1-to-1).
     */
    public function preference()
    {
        return $this->hasOne(UserPreference::class, 'user_id');
    }

    /**
     * Riwayat pesanan makanan surplus oleh pengguna (1-to-Many).
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Riwayat ulasan yang diberikan pengguna (1-to-Many).
     */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    /**
     * Gerai Mitra UMKM yang dikelola pengguna (1-to-1).
     */
    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'user_id');
    }
}
