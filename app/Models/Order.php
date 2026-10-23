<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'order_number',
        'user_id',
        'merchant_id',
        'total_items_price',
        'total_packaging_fee',
        'service_fee',
        'total_price',
        'money_saved',
        'weight_saved_grams',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'customer_nim',
        'notes',
        'pickup_time',
        'pickup_deadline',
        'arrival_estimate',
        'remind_before',
        'status',
        'qr_token',
        'has_review',
        'completed_at',
    ];

    protected $casts = [
        'total_items_price' => 'integer',
        'total_packaging_fee' => 'integer',
        'service_fee' => 'integer',
        'total_price' => 'integer',
        'money_saved' => 'integer',
        'weight_saved_grams' => 'integer',
        'remind_before' => 'boolean',
        'has_review' => 'boolean',
        'completed_at' => 'datetime',
    ];

    /**
     * Accessor yang otomatis diserialisasi ke JSON/Inertia props untuk Vue.
     */
    protected $appends = [
        'customerName',
        'nim',
        'whatsapp',
        'pickupTime',
        'pickupDeadline',
        'total',
        'food',
        'quantity',
        'ecoPackaging',
        'ecoPackagingLabel',
        'customerNote',
        'paymentMethod',
        'paymentStatus',
        'qrCode',
        'statusLabel',
        'date',
    ];

    /**
     * Relasi ke pemesan (Mahasiswa / User).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke toko mitra kuliner.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    /**
     * Relasi ke rincian porsi makanan dalam pesanan (3NF order_items).
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi ke data transaksi pembayaran gateway (3NF payments).
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope untuk pesanan yang masih menunggu pengambilan.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'menunggu');
    }

    /**
     * Scope untuk pesanan yang siap diambil.
     */
    public function scopeReady(Builder $query): Builder
    {
        return $query->where('status', 'siap_ambil');
    }

    /**
     * Scope untuk pesanan yang sudah selesai diverifikasi QR.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }

    /**
     * Label status bahasa Indonesia yang ramah antarmuka.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'menunggu' => 'Menunggu Pickup',
            'siap_ambil' => 'Siap Diambil',
            'selesai' => 'Selesai Diambil',
            'batal' => 'Dibatalkan',
            default => 'Diproses',
        };
    }

    public function getCustomerNameAttribute(): string
    {
        return $this->customer_name ?? ($this->user?->name ?? 'Mahasiswa');
    }

    public function getNimAttribute(): string
    {
        return $this->customer_nim ?? ($this->user?->nim ?? '23051204088');
    }

    public function getWhatsappAttribute(): string
    {
        return $this->customer_phone ?? ($this->user?->phone ?? '0812-3456-7890');
    }

    public function getPickupTimeAttribute(): string
    {
        return $this->attributes['pickup_time'] ?? '19.30 – 20.30 WIB';
    }

    public function getPickupDeadlineAttribute(): string
    {
        return $this->attributes['pickup_deadline'] ?? 'Hari ini tutup 21.00 WIB';
    }

    public function getTotalAttribute(): int
    {
        return (int) ($this->total_price ?? 0);
    }

    public function getFoodAttribute(): array
    {
        $item = $this->items?->first();
        $foodModel = $item?->food;

        $name = $foodModel?->name ?? $item?->food_name ?? 'Menu Makanan Surplus';
        $shortName = strlen($name) > 25 ? substr($name, 0, 22) . '...' : $name;

        return [
            'name' => $name,
            'shortName' => $shortName,
            'priceDiscount' => (int) ($item?->price ?? $foodModel?->price_discount ?? 12000),
            'priceOriginal' => (int) ($foodModel?->price_original ?? 25000),
            'merchant' => $this->merchant?->name ?? 'Warung Bu Sari',
            'image' => $foodModel?->image ?? 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?auto=format&fit=crop&w=500&q=80',
        ];
    }

    public function getQuantityAttribute(): int
    {
        $sum = $this->items?->sum('quantity');
        return $sum && $sum > 0 ? (int) $sum : 1;
    }

    public function getEcoPackagingAttribute(): string
    {
        return $this->items?->first()?->eco_packaging ?? 'bawa_sendiri';
    }

    public function getEcoPackagingLabelAttribute(): string
    {
        return match ($this->ecoPackaging) {
            'bawa_sendiri' => 'Bawa Wadah Sendiri (Rp0)',
            'kotak_kertas' => 'Kotak Kertas Eco (Rp1.000)',
            'thinwall' => 'Thinwall Food Grade (Rp2.000)',
            default => 'Standar Warung',
        };
    }

    public function getCustomerNoteAttribute(): string
    {
        return $this->notes ?? '';
    }

    public function getPaymentMethodAttribute(): string
    {
        return $this->payment?->payment_method ?? 'kasir';
    }

    public function getPaymentStatusAttribute(): string
    {
        return $this->payment?->status ?? 'menunggu_bayar';
    }

    public function getQrCodeAttribute(): string
    {
        return $this->qr_token ?? ($this->order_number . '-PF');
    }

    public function getDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->translatedFormat('d M Y, H:i') : 'Hari ini';
    }
}
