<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'owner_name',
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

    protected $casts = [
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
