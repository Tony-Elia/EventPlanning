<?php

namespace Modules\Service\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceCategory extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Get all services in this category.
     */
    public function services(): ServiceCategory|\Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Service::class, 'category_id');
    }
}
