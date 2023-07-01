<?php

namespace Tests\Feature;

use App\Enums\OrderStatusesEnum;
use App\Http\Resources\OrderResource;
use App\Models\Delivery;
use App\Models\Order;
use App\Models\OrderDestinationDetail;
use App\Models\OrderSourceDetail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeliveryTest extends TestCase
{
    public function test_delivery_can_see_list_of_pending_orders(): void
    {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => null,
            'status' => OrderStatusesEnum::PENDING->value
        ])->create();

        Order::factory()->state([
            'delivery_id' => $delivery->id,
            'status' => OrderStatusesEnum::ACCEPT->value
        ])->create();

        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $response = $this->get('api/v1/delivery/orders/pending');

        $response->assertStatus(200);
        $this->assertResponseContainsOrders($response, $order);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_delivery_can_see_list_of_own_accepted_orders(): void
    {
        $delivery = Delivery::factory()->create();
        $delivery2 = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => $delivery->id,
            'status' => OrderStatusesEnum::ACCEPT->value
        ])->create();
        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $order2 = Order::factory()->state([
            'delivery_id' => null,
            'status' => OrderStatusesEnum::PENDING->value
        ])->create();
        OrderDestinationDetail::factory()->for($order2)->create();
        OrderSourceDetail::factory()->for($order2)->create();

        $order3 = Order::factory()->state([
            'delivery_id' => $delivery2->id,
            'status' => OrderStatusesEnum::ACCEPT->value
        ])->create();
        OrderDestinationDetail::factory()->for($order3)->create();
        OrderSourceDetail::factory()->for($order3)->create();

        $response = $this->get('api/v1/delivery/orders/accepted');

        $response->assertStatus(200);
        $this->assertResponseContainsOrders($response, $order);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_delivery_can_change_pending_order_to_accept()
    {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => null,
            'status' => OrderStatusesEnum::PENDING->value
        ])->create();
        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $response = $this->patch("api/v1/delivery/orders/{$order->uuid}/accept");

        $response->assertStatus(200);
        $this->assertTrue($response->json('is_assigned'));
        $this->assertSame(OrderStatusesEnum::ACCEPT->value, $order->refresh()->status->value);
        $this->assertSame($delivery->id, $order->refresh()->delivery_id);
        Notification::assertCount(1);
    }

    public function test_delivery_can_change_accepted_order_to_going_to_source_location()
    {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => $delivery->id,
            'status' => OrderStatusesEnum::ACCEPT->value
        ])->create();
        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $response = $this->patch("api/v1/delivery/orders/{$order->uuid}/go-to-source-location", [
            'lat' => 44.968046,
            'long' => -94.420307
        ]);

        $response->assertStatus(200);
        $this->assertSame(OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value, $order->refresh()->status->value);
        Notification::assertCount(1);
    }

    public function test_delivery_can_change_going_to_source_location_order_to_going_to_destination_location()
    {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => $delivery->id,
            'status' => OrderStatusesEnum::GOING_TO_SOURCE_LOCATION->value
        ])->create();
        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $response = $this->patch("api/v1/delivery/orders/{$order->uuid}/go-to-destination-location", [
            'lat' => 44.968046,
            'long' => -94.420307
        ]);

        $response->assertStatus(200);
        $this->assertSame(OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value, $order->refresh()->status->value);
        Notification::assertCount(1);
    }

    public function test_delivery_can_change_going_to_destination_location_order_to_done()
    {
        $delivery = Delivery::factory()->create();

        Sanctum::actingAs($delivery, ['delivery']);

        $order = Order::factory()->state([
            'delivery_id' => $delivery->id,
            'status' => OrderStatusesEnum::GOING_TO_DESTINATION_LOCATION->value
        ])->create();
        OrderDestinationDetail::factory()->for($order)->create();
        OrderSourceDetail::factory()->for($order)->create();

        $response = $this->patch("api/v1/delivery/orders/{$order->uuid}/done", [
            'lat' => 44.968046,
            'long' => -94.420307
        ]);

        $response->assertStatus(200);
        $this->assertSame(OrderStatusesEnum::DONE->value, $order->refresh()->status->value);
        Notification::assertCount(1);
    }

    private function assertResponseContainsOrders(TestResponse $response, ...$orders): void
    {
        $response->assertJson([
            'data' => array_map(function (Order $order) {
                return $this->OrderToResourceResponse($order);
            }, $orders),
        ]);
    }

    private function OrderToResourceResponse(Order $order)
    {
        return json_decode(OrderResource::make($order)->toJson(), true);
    }
}
