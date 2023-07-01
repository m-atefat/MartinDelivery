<?php

namespace Tests\Feature;

use App\Enums\OrderStatusesEnum;
use App\Models\Business;
use App\Models\Order;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BusinessTest extends TestCase
{
    public function test_business_can_create_order(): void
    {
        $business = Business::factory()->create();

        Sanctum::actingAs($business, ['business']);

        $response = $this->post('api/v1/business/orders', [
            'source_name' => 'ali',
            'source_address' => 'tehran',
            'source_phone' => '09121456987',
            'source_lat' => 56.156986,
            'source_long' => -80.696278,
            'destination_name' => 'hasan',
            'destination_address' => 'tehran, azadi',
            'destination_phone' => '0912658974',
            'destination_lat' => 50.156986,
            'destination_long' => -70.696278
        ]);

        $response->assertStatus(201);
        $this->assertArrayHasKey('order_uuid', $response->json());
    }


    public function test_business_can_cancel_order(): void
    {
        $business = Business::factory()->create();

        Sanctum::actingAs($business, ['business']);

        $order = Order::factory()->state([
            'delivery_id' => null,
            'business_id' => $business->id,
            'status' => OrderStatusesEnum::ACCEPT->value
        ])->create();

        $response = $this->patch("api/v1/business/orders/{$order->uuid}/cancel");
        $response->assertStatus(200);
        $this->assertArrayHasKey('status', $response->json());
        $this->assertArrayHasKey('uuid', $response->json());
        $this->assertSame(OrderStatusesEnum::CANCEL->value, $order->refresh()->status->value);
        $this->assertSame($response->json('uuid'), $order->refresh()->uuid);
    }
}
