<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allowDisplayDeliveryLocation = $this->additional['allowDisplayDeliveryLocation'] ? $this->additional['allowDisplayDeliveryLocation'] : false;

        return [
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            $this->mergeWhen($allowDisplayDeliveryLocation, [
                'latitude' => $this->lat,
                'longitude' => $this->long,
            ])
        ];
    }
}
