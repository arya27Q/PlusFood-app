<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $table = 'user_preferences';

    protected $fillable = [
        'user_id',
        'campus_zone',
        'radius',
        'prefer_halal',
        'prefer_vegetarian',
        'prefer_nut_free',
        'prefer_not_spicy',
    ];

    protected function casts(): array
    {
        return [
            'prefer_halal' => 'boolean',
            'prefer_vegetarian' => 'boolean',
            'prefer_nut_free' => 'boolean',
            'prefer_not_spicy' => 'boolean',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
