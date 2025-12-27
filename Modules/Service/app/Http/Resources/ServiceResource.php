<?php

namespace Modules\Service\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Service\Models\Service;

/** @mixin Service */
class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'price_unit' => $this->price_unit,
            'location' => $this->location,
            'is_active' => $this->is_active,
            'type' => $this->type,
            'capacity' => $this->capacity,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'amenities' => $this->amenities,
            'reviews_count' => $this->whenHas('reviews_count'),

            'category_id' => new ServiceCategoryResource($this->whenLoaded('category')),

            'provider' => new UserResource($this->whenLoaded('provider')),
        ];
    }
}
