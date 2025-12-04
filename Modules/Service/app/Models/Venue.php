<?php

namespace Modules\Service\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_id',
        'name',
        'description',
        'capacity',
        'base_price',
        'price_unit',
        'location',
        'address',
        'latitude',
        'longitude',
        'amenities',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'base_price' => 'decimal:2',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'amenities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the provider that owns the venue.
     */
    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    /**
     * Get all reviews for the venue.
     */
    public function reviews()
    {
        return $this->morphMany(Review::class, 'reviewable');
    }
}
