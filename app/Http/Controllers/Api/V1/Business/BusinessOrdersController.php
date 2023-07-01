<?php

namespace App\Http\Controllers\Api\V1\Business;

use App\Dtos\OrderDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateBusinessOrderRequest;
use App\Services\BusinessService\BusinessService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

class BusinessOrdersController extends Controller
{
    public function __construct(private readonly BusinessService $businessService)
    {

    }

    public function createOrder(CreateBusinessOrderRequest $request): JsonResponse
    {
        $orderDto = new OrderDto();
        $orderDto->setBusinessId(Auth::id())
            ->setOrderSourceName($request->source_name)
            ->setOrderSourceAddress($request->source_address)
            ->setOrderSourcePhone($request->source_phone)
            ->setOrderSourceLat($request->source_lat)
            ->setOrderSourceLong($request->source_long)
            ->setOrderDestinationName($request->destination_name)
            ->setOrderDestinationAddress($request->destination_address)
            ->setOrderDestinationPhone($request->destination_phone)
            ->setOrderDestinationLat($request->destination_lat)
            ->setOrderDestinationLong($request->destination_long);

        $order = $this->businessService->createOrder($orderDto);
        return response()->json([
            'order_uuid' => $order->uuid
        ], 201);
    }

    /**
     * @throws Throwable
     */
    public function cancelOrder(string $uuid): JsonResponse
    {
        $order = $this->businessService->cancelOrder(Auth::id(), $uuid);
        return response()->json([
            'uuid' => $order->uuid,
            'status' => $order->status->value
        ]);
    }
}
