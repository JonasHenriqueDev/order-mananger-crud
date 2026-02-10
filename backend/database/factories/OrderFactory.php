<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper(fake()->bothify('########')),
            'status' => OrderStatus::PENDING,
            'subtotal' => fake()->randomFloat(2, 10, 1000),
            'tax' => fake()->randomFloat(2, 0, 50),
            'discount' => fake()->randomFloat(2, 0, 100),
            'total' => fake()->randomFloat(2, 10, 1000),
            'notes' => fake()->optional()->sentence(),
            'processed_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PENDING,
            'processed_at' => null,
            'completed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    public function processing(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::PROCESSING,
            'processed_at' => now(),
            'completed_at' => null,
            'cancelled_at' => null,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::COMPLETED,
            'processed_at' => now()->subHours(2),
            'completed_at' => now(),
            'cancelled_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::CANCELLED,
            'processed_at' => null,
            'completed_at' => null,
            'cancelled_at' => now(),
        ]);
    }
}

