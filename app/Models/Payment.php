<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = [
        'order_id',
        'payment_method',
        'payment_gateway',
        'payment_reference',
        'amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];

    /**
     * Relasi ke pesanan terkait.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Cek apakah pembayaran sudah lunas.
     */
    public function isPaid(): bool
    {
        return $this->status === 'lunas';
    }

    /**
     * Label metode pembayaran bahasa Indonesia.
     */
    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'qris' => 'QRIS (Sandbox Instant)',
            'gopay' => 'GoPay (Sandbox)',
            'dana' => 'DANA (Sandbox)',
            'kasir' => 'Bayar di Kasir (Tunai)',
            default => strtoupper($this->payment_method),
        };
    }
}
