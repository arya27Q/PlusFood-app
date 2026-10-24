<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Merchant extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'merchants';

    protected $fillable = [
        'user_id',
        'name',
        'owner_name',
        'email',
        'password',
        'slug',
        'nib',
        'campus_zone',
        'campus_name',
        'category',
        'address',
        'canteen_location',
        'phone',
        'rating',
        'reviews_count',
        'is_verified',
        'operating_hours',
        'default_session',
        'support_eco',
        'bank_name',
        'bank_account',
        'bank_holder',
        'balance_rp',
        'total_revenue_rp',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'is_verified' => 'boolean',
        'support_eco' => 'boolean',
        'balance_rp' => 'integer',
        'total_revenue_rp' => 'integer',
    ];

    protected $appends = [
        'initial',
    ];

    /**
     * Relasi ke pemilik mitra (User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke daftar menu makanan surplus yang dijual merchant.
     */
    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    /**
     * Relasi ke daftar pesanan yang masuk ke toko mitra ini.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Relasi ke daftar ulasan mahasiswa untuk mitra ini.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Inisial 2 huruf untuk avatar warung (e.g. "Warung Bu Sari" -> "BS").
     */
    public function getInitialAttribute(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }

        return strtoupper(substr($this->name, 0, 2));
    }
}
