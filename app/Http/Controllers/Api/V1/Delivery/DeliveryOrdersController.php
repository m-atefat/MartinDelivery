<?php

namespace App\Http\Controllers\Api\V1\Delivery;

use App\Enums\OrderStatusesEnum;
use App\Exceptions\OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetOrderStatusRequest;
use App\Http\Resources\OrderResource;
use App\Services\DeliveryService\DeliveryService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class DeliveryOrdersController extends Controller
{
    public function __construct(private readonly DeliveryService $deliveryService)
    {

    }

    public function ordersInPendingStatus(Request $request): AnonymousResourceCollection
    {
        $orders = $this->deliveryService->getPendingOrders(
            $request->query('page', 1),
            $request->query('perPage', 10)
        );

        return OrderResource::collection($orders);
    }

    public function acceptedOrders(Request $request): AnonymousResourceCollection
    {
        $orders = $this->deliveryService->getOrdersInSpecificStatus(
            Auth::id(),
            $request->query('page', 1),
            $request->query('perPage', 10),
            OrderStatusesEnum::ACCEPT->value
        );

        return OrderResource::collection($orders);
    }

    /**
     * @throws Exception
     */
    public function assignOrder(string $uuid): JsonResponse
    {
        $order = $this->deliveryService->assignOrder(Auth::id(), $uuid);
        return response()->json([
            'is_assigned' => $order->status->value === OrderStatusesEnum::ACCEPT->value
        ]);
    }

    /**
     * @throws Throwable
     * @throws OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException
     */
    public function updateOrderStatusToGoingToSourceLocation(string $uuid, SetOrderStatusRequest $request): OrderResource
    {
        $order = $this->deliveryService->updateOrderStatusToGoingToSourceLocation($uuid, Auth::id(), $request->lat, $request->long);
        return OrderResource::make($order);
    }

    /**
     * @throws Throwable
     * @throws OrderStatusShouldBeOnlyAcceptedToChangeToGoingToSourceLocationStatusException
     */
    public function updateOrderStatusToGoingToDestinationLocation(string $uuid, SetOrderStatusRequest $request): OrderResource
    {
        $order = $this->deliveryService->updateOrderStatusToGoingToDestinationLocation($uuid, Auth::id(), $request->lat, $request->long);
        return OrderResource::make($order);
    }

    /**
     * @throws Throwable
     */
    public function updateOrderStatusToDone(string $uuid, SetOrderStatusRequest $request): OrderResource
    {
        $order = $this->deliveryService->updateOrderStatusToDone($uuid, Auth::id(), $request->lat, $request->long);
        return OrderResource::make($order);
    }
}
