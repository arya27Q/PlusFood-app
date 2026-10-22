<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'food_id',
        'food_name',
        'price',
        'quantity',
        'subtotal',
        'eco_packaging',
        'packaging_fee',
        'weight_grams',
    ];

    protected $casts = [
        'price' => 'integer',
        'quantity' => 'integer',
        'subtotal' => 'integer',
        'packaging_fee' => 'integer',
        'weight_grams' => 'integer',
    ];

    /**
     * Relasi ke header pesanan utama.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke data master makanan surplus.
     */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    /**
     * Label wadah kemasan ramah lingkungan.
     */
    public function getPackagingLabelAttribute(): string
    {
        return match ($this->eco_packaging) {
            'bawa_sendiri' => 'Bawa Wadah Sendiri (Rp0)',
            'kotak_kertas' => 'Kotak Kertas Eco (Rp1.000)',
            'thinwall' => 'Thinwall Food Grade (Rp2.000)',
            default => 'Standar Warung',
        };
    }
}
