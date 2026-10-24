<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'food_id',
        'merchant_id',
        'rating',
        'comment',
        'tags',
        'is_anonymous',
        'reward_points',
    ];

    protected $casts = [
        'rating' => 'integer',
        'tags' => 'array',
        'is_anonymous' => 'boolean',
        'reward_points' => 'integer',
    ];

    /**
     * Relasi ke pesanan yang diulas.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke mahasiswa / user yang menulis ulasan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke makanan surplus yang diulas.
     */
    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    /**
     * Relasi ke gerai mitra yang diulas.
     */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
