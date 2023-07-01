<?php

namespace Database\Factories;

use App\Enums\OrderStatusesEnum;
use App\Models\Business;
use App\Models\Delivery;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uuid'        => $this->faker->uuid(),
            'delivery_id' => Delivery::factory(),
            'business_id' => Business::factory(),
            'status'      => $this->faker->randomElement(OrderStatusesEnum::values())
        ];
    }
}
