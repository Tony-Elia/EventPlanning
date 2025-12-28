<?php

namespace Modules\Service\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Service\Models\PackageItem;

/** @mixin PackageItem */
class PackageItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'package_id' => $this->package_id,
            'service_id' => $this->service_id,

            'package' => new ServicePackageResource($this->whenLoaded('package')),
            'service' => new ServiceResource($this->whenLoaded('service')),
        ];
    }
}
