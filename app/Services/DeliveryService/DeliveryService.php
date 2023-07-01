<?php

namespace App\Services\DeliveryService;

use App\Enums\OrderStatusesEnum;
use App\Exceptions\BikeDeliveryHasAnotherIncompleteOrderException;
use App\Exceptions\OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException;
use App\Exceptions\OrderStatusShouldBeOnlyGoingToDestinationLocationToChangeToDoneStatusException;
use App\Exceptions\OrderStatusShouldBeOnlyGoingToSourceLocationToChangeToGoingToDestinationLocationStatusException;
use App\Models\Delivery;
use App\Models\Order;
use App\Notifications\BusinessOrderNotification;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeliveryService
{
    public function getPendingOrders(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        $orderQuery = Order::query()
            ->where('status', OrderStatusesEnum::PENDING->value)
            ->with(['sourceDetails', 'destinationDetails', 'business']);

        return $this->makeQueryAsPagination($orderQuery, $page, $perPage);
    }

    public function getOrdersInSpecificStatus(int $deliveryId, int $page = 1, int $perPage = 10, string $status = null): LengthAwarePaginator
    {
        $orderQuery = Order::query()
            ->where('delivery_id', $deliveryId)
            ->when($status, function (Builder $builder) use ($status) {
                $builder->where('status', $status);
            })
            ->with(['sourceDetails', 'destinationDetails', 'business']);

        return $this->makeQueryAsPagination($orderQuery, $page, $perPage);
    }

    /**
     * @throws OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException
     * @throws Throwable
     */
    public function updateOrderStatusToGoingToSourceLocation(string $orderUuid, int $deliveryId, float $lat, float $long): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('uuid', $orderUuid)
            ->with(['sourceDetails', 'destinationDetails', 'business', 'delivery'])
            ->firstOrFail();

        throw_if(
            $order->status->value !== OrderStatusesEnum::ACCEPT->value, OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException::class
        );

        $hasAnotherOrderInGoingToSourceOrDestinationLocationStatus = Order::query()
            ->where('delivery_id', $deliveryId)
            ->whereIn('status', [OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value, OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value])
            ->exists();

        throw_if($hasAnotherOrderInGoingToSourceOrDestinationLocationStatus === true, BikeDeliveryHasAnotherIncompleteOrderException::class);

        $this->setDeliveryLocation($order->delivery, $lat, $long);

        $order->update([
            'status' => OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value
        ]);

        $order->business->notify(new BusinessOrderNotification($order));

        return $order;
    }


    /**
     * @throws OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException
     * @throws Throwable
     */
    public function updateOrderStatusToGoingToDestinationLocation(string $orderUuid, int $deliveryId, float $lat, float $long): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('delivery_id', $deliveryId)
            ->where('uuid', $orderUuid)
            ->with(['sourceDetails', 'destinationDetails', 'business', 'delivery'])
            ->firstOrFail();

        throw_if(
            $order->status->value !== OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value, OrderStatusShouldBeOnlyGoingToSourceLocationToChangeToGoingToDestinationLocationStatusException::class
        );

        $this->setDeliveryLocation($order->delivery, $lat, $long);

        $order->update([
            'status' => OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value
        ]);

        $order->business->notify(new BusinessOrderNotification($order));

        return $order;
    }

    /**
     * @throws Throwable
     */
    public function updateOrderStatusToDone(string $orderUuid, int $deliveryId, float $lat, float $long): Order
    {
        /** @var Order $order */
        $order = Order::query()
            ->where('uuid', $orderUuid)
            ->where('delivery_id', $deliveryId)
            ->with(['sourceDetails', 'destinationDetails', 'business', 'delivery'])
            ->firstOrFail();

        throw_if(
            $order->status->value !== OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value, OrderStatusShouldBeOnlyGoingToDestinationLocationToChangeToDoneStatusException::class
        );

        $this->setDeliveryLocation($order->delivery, $lat, $long);

        $order->update([
            'status' => OrderStatusesEnum::DONE->value
        ]);

        $order->business->notify(new BusinessOrderNotification($order));

        return $order;
    }

    private function makeQueryAsPagination(Builder $query, int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    private function setDeliveryLocation(Delivery $delivery, float $lat, float $long): void
    {
        $delivery->update([
            'lat' => $lat,
            'long' => $long
        ]);
    }

    /**
     * @throws Exception
     */
    public function assignOrder(int $deliveryId, string $uuid): Order
    {
        try {
            DB::beginTransaction();

            /** @var Order $order */
            $order = Order::query()
                ->whereNull('delivery_id')
                ->where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $order->update([
                'delivery_id' => $deliveryId,
                'status' => OrderStatusesEnum::ACCEPT->value
            ]);

            $order->business->notify(new BusinessOrderNotification($order));

            DB::commit();
            return $order;
        } catch (Exception $exception) {
            DB::rollBack();
            throw new $exception();
        }
    }
}
