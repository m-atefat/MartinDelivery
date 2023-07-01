<?php

namespace App\Services\BusinessService;

use App\Dtos\OrderDto;
use App\Enums\OrderStatusesEnum;
use App\Exceptions\CanNotCancelOrderAfterReciveByDeliveryException;
use App\Models\Order;
use Illuminate\Support\Str;
use Throwable;

class BusinessService
{
    public function createOrder(OrderDto $orderDto): Order
    {
        /** @var Order $order */
        $order = Order::query()->create([
            'uuid' => Str::uuid(),
            'business_id' => $orderDto->getBusinessId(),
            'status' => OrderStatusesEnum::PENDING
        ]);

        $order->sourceDetails()->create([
            'name' => $orderDto->getOrderSourceName(),
            'address' => $orderDto->getOrderSourceAddress(),
            'phone' => $orderDto->getOrderSourcePhone(),
            'lat' => $orderDto->getOrderSourceLat(),
            'long' => $orderDto->getOrderSourceLong(),
        ]);

        $order->destinationDetails()->create([
            'name' => $orderDto->getOrderDestinationName(),
            'address' => $orderDto->getOrderDestinationAddress(),
            'phone' => $orderDto->getOrderDestinationPhone(),
            'lat' => $orderDto->getOrderDestinationLat(),
            'long' => $orderDto->getOrderDestinationLong(),
        ]);

        return $order;
    }

    /**
     * @throws Throwable
     */
    public function cancelOrder(int $businessId, string $orderUuid): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('business_id', $businessId)
            ->where('uuid', $orderUuid)
            ->firstOrFail();

        throw_if(!$order->canCancel(), CanNotCancelOrderAfterReciveByDeliveryException::class);

        $order->update([
            'status' => OrderStatusesEnum::CANCEL->value
        ]);

        return $order;
    }
}
