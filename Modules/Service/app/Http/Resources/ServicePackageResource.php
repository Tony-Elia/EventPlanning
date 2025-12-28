<?php

namespace Modules\Service\Http\Resources;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Service\Models\ServicePackage;

/** @mixin ServicePackage */
class ServicePackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'items_count' => $this->whenHas('items_count'),
            'items' => PackageItemResource::collection($this->whenLoaded('items')),
            'provider_id' => $this->provider_id,

            'provider' => new UserResource($this->whenLoaded('provider')),
        ];
    }
}
