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
}
