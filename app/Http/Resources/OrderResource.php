<?php

namespace App\Http\Resources;

use App\Enums\OrderStatusesEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $allowDisplayDeliveryLocation = false;
        if (
            $this->status->value === OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value ||
            $this->status->value === OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value
        ){
            $allowDisplayDeliveryLocation = true;
        }

        return [
            'id' => $this->uuid,
            'delivery_information' => DeliveryResource::make($this->whenLoaded('delivery'))->additional([
                'allowDisplayDeliveryLocation' => $allowDisplayDeliveryLocation]
            ),
            'business_information' => BusinessResource::make($this->whenLoaded('business')),
            'status' => $this->status,
            'source_details' => SourceOrderDetailResource::make($this->whenLoaded('sourceDetails')),
            'destination_details' => DestinationOrderDetailResource::make($this->whenLoaded('destinationDetails')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
