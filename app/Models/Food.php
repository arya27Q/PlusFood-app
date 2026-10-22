<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    use HasFactory;

    protected $table = 'foods';

    protected $fillable = [
        'merchant_id',
        'name',
        'slug',
        'category',
        'description',
        'price_original',
        'price_discount',
        'discount_percent',
        'stock',
        'stock_initial',
        'weight_grams',
        'pickup_time_start',
        'pickup_time_end',
        'pickup_time_display',
        'pickup_deadline_info',
        'dietary_tags',
        'is_sealed',
        'is_cooked_today',
        'image',
        'status',
    ];

    protected $casts = [
        'price_original' => 'integer',
        'price_discount' => 'integer',
        'discount_percent' => 'integer',
        'stock' => 'integer',
        'stock_initial' => 'integer',
        'weight_grams' => 'integer',
        'dietary_tags' => 'array',
        'is_sealed' => 'boolean',
        'is_cooked_today' => 'boolean',
    ];

    /**
     * Accessor yang otomatis diserialisasi ke JSON/Inertia props.
     */
    protected $appends = [
        'priceDiscount',
        'priceOriginal',
        'discountPercent',
        'shortName',
        'pickupTime',
        'pickupDeadlineInfo',
        'co2Saved',
        'merchantName',
        'merchantInitial',
        'merchantRating',
        'merchantReviewsCount',
        'campusZone',
        'merchantZoneLabel',
        'address',
        'distance',
        'distanceMins',
        'minutesLeft',
        'packages',
        'dietaryTags',
        'tags',
        'rating',
        'reviewsCount',
        'batchCooked',
        'warmerGrade',
        'isCookedToday',
        'isSealed',
        'isVerified',
        'distanceKm',
    ];

    /**
     * Relasi ke mitra / warung pemilik menu.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Scope makanan yang aktif dan masih memiliki stok.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'aktif')->where('stock', '>', 0);
    }

    /**
     * Scope filter berdasarkan kategori makanan.
     */
    public function scopeCategory(Builder $query, ?string $category): Builder
    {
        if ($category && $category !== 'Semua') {
            return $query->where('category', $category);
        }

        return $query;
    }

    /**
     * Scope filter berdasarkan zona kampus mitra.
     */
    public function scopeCampusZone(Builder $query, ?string $zone): Builder
    {
        if ($zone && $zone !== 'all') {
            return $query->whereHas('merchant', function (Builder $q) use ($zone) {
                $q->where('campus_zone', $zone);
            });
        }

        return $query;
    }

    /**
     * Scope pencarian kata kunci makanan atau warung.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if ($keyword = trim($keyword ?? '')) {
            return $query->where(function (Builder $q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%")
                  ->orWhereHas('merchant', function (Builder $mq) use ($keyword) {
                      $mq->where('name', 'like', "%{$keyword}%");
                  });
            });
        }

        return $query;
    }

    /**
     * Scope filter harga maksimal.
     */
    public function scopeMaxPrice(Builder $query, ?int $maxPrice): Builder
    {
        if ($maxPrice && $maxPrice > 0) {
            return $query->where('price_discount', '<=', $maxPrice);
        }

        return $query;
    }

    // ================= ACCESSORS UNTUK INTEGRASI VUE =================

    public function getPriceDiscountAttribute(): int
    {
        return (int) $this->attributes['price_discount'];
    }

    public function getPriceOriginalAttribute(): int
    {
        return (int) $this->attributes['price_original'];
    }

    public function getDiscountPercentAttribute(): int
    {
        return (int) $this->attributes['discount_percent'];
    }

    public function getShortNameAttribute(): string
    {
        return $this->name;
    }

    public function getPickupTimeAttribute(): string
    {
        return $this->pickup_time_display ?? '18.00 – 21.00 WIB';
    }

    public function getPickupDeadlineInfoAttribute(): string
    {
        return $this->attributes['pickup_deadline_info'] ?? 'Hari ini sebelum tutup 21.00 WIB';
    }

    public function getCo2SavedAttribute(): string
    {
        $kg = ($this->weight_grams * 2.1) / 1000;
        return number_format($kg, 2, ',', '.') . ' kg CO2e';
    }

    public function getMerchantNameAttribute(): string
    {
        return $this->merchant?->name ?? 'Warung Kampus';
    }

    public function getMerchantInitialAttribute(): string
    {
        return $this->merchant?->initial ?? 'PF';
    }

    public function getMerchantRatingAttribute(): float
    {
        return (float) ($this->merchant?->rating ?? 4.8);
    }

    public function getMerchantReviewsCountAttribute(): int
    {
        return (int) ($this->merchant?->reviews_count ?? 50);
    }

    public function getCampusZoneAttribute(): string
    {
        return $this->merchant?->campus_zone ?? 'unesa-ketintang';
    }

    public function getMerchantZoneLabelAttribute(): string
    {
        return $this->merchant?->campus_name ?? 'Kampus Surabaya';
    }

    public function getAddressAttribute(): string
    {
        return $this->merchant?->address ?? 'Kantin Kampus Surabaya';
    }

    public function getDistanceKmAttribute(): float
    {
        $distances = [
            'unesa-ketintang' => 1.2,
            'its-sukolilo' => 0.8,
            'unair-b' => 0.7,
            'unesa-lidah' => 1.5,
            'upn-rungkut' => 0.8,
            'petra-siwalankerto' => 0.5,
        ];

        return (float) ($distances[$this->campusZone] ?? 1.2);
    }

    public function getDistanceAttribute(): string
    {
        return str_replace('.', ',', (string) $this->distanceKm) . ' km';
    }

    public function getDistanceMinsAttribute(): string
    {
        $campus = $this->merchant?->campus_name ?? 'Kampus';
        $mins = (int) round($this->distanceKm * 6);
        return "{$mins} mnt dari {$campus}";
    }

    public function getMinutesLeftAttribute(): int
    {
        return 45;
    }

    public function getDietaryTagsAttribute(): array
    {
        $raw = $this->attributes['dietary_tags'] ?? null;
        if (is_array($raw)) {
            return $raw;
        }
        if (is_string($raw)) {
            return json_decode($raw, true) ?: [];
        }
        return [];
    }

    public function getTagsAttribute(): array
    {
        return $this->dietaryTags;
    }

    public function getRatingAttribute(): float
    {
        return (float) ($this->merchant?->rating ?? 4.8);
    }

    public function getReviewsCountAttribute(): int
    {
        return (int) ($this->merchant?->reviews_count ?? 148);
    }

    public function getBatchCookedAttribute(): string
    {
        return 'Sore Ini (Fresh)';
    }

    public function getWarmerGradeAttribute(): string
    {
        return 'Food Warmer Grade A';
    }

    public function getIsCookedTodayAttribute(): bool
    {
        return (bool) ($this->is_cooked_today ?? true);
    }

    public function getIsSealedAttribute(): bool
    {
        return (bool) ($this->is_sealed ?? true);
    }

    public function getIsVerifiedAttribute(): bool
    {
        return (bool) ($this->merchant?->is_verified ?? true);
    }

    /**
     * Rincian paket bawaan yang divisualisasikan di DetailProduk.vue.
     */
    public function getPackagesAttribute(): array
    {
        if ($this->category === 'Nasi') {
            return [
                ['icon' => 'rice', 'title' => '1 Porsi Nasi Pulen'],
                ['icon' => 'chicken', 'title' => 'Lauk Utama Porsi Kenyang'],
                ['icon' => 'sambal', 'title' => 'Sambal Segar & Lalapan'],
                ['icon' => 'tempeh', 'title' => 'Pelengkap Tahu/Tempe Crispy'],
            ];
        }

        if ($this->category === 'Soto') {
            return [
                ['icon' => 'soup', 'title' => 'Kuah Soto Kaldu Gurih'],
                ['icon' => 'chicken', 'title' => 'Daging / Ayam Suwir Lembut'],
                ['icon' => 'cracker', 'title' => 'Koya Gurih / Kerupuk'],
                ['icon' => 'sambal', 'title' => 'Sambal Cabe Segar'],
            ];
        }

        if ($this->category === 'Mie') {
            return [
                ['icon' => 'noodles', 'title' => '1 Porsi Mie Kenyal'],
                ['icon' => 'egg', 'title' => 'Telur & Suwir Ayam'],
                ['icon' => 'salad', 'title' => 'Acar Timun Segar'],
            ];
        }

        if ($this->category === 'Roti') {
            return [
                ['icon' => 'bread', 'title' => '1 Kotak Roti Porsi Kenyang'],
                ['icon' => 'cheese', 'title' => 'Isian Manis & Lembut'],
            ];
        }

        return [
            ['icon' => 'snack', 'title' => '1 Porsi Menu Siap Santap'],
            ['icon' => 'sauce', 'title' => 'Saus / Sambal Pendamping'],
        ];
    }
}
