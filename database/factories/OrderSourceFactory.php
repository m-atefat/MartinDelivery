<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderSource>
 */
class OrderSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'name' => $this->faker->name(),
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'lat'   => $this->faker->latitude(),
            'long'  => $this->faker->longitude()
        ];
    }
}
