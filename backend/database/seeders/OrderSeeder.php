<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $products = Product::where('status', 'active')->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            $this->command->warn('No users or products found. Please seed users and products first.');
            return;
        }

        $this->command->info('Creating orders...');

        foreach ($users->random(min(5, $users->count())) as $user) {
            $this->createOrder($user, $products, OrderStatus::PENDING, 2);
            $this->createOrder($user, $products, OrderStatus::PROCESSING, 1);
            $this->createOrder($user, $products, OrderStatus::COMPLETED, 3);
            $this->createOrder($user, $products, OrderStatus::CANCELLED, 1);
        }

        $this->command->info('Orders created successfully!');
    }

    private function createOrder($user, $products, OrderStatus $status, int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            $order = Order::factory()->create([
                'user_id' => $user->id,
                'status' => $status,
                'tax' => rand(0, 50),
                'discount' => rand(0, 20),
            ]);

            $itemCount = rand(1, 4);
            $randomProducts = $products->random(min($itemCount, $products->count()));

            foreach ($randomProducts as $product) {
                $quantity = rand(1, 3);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_sku' => $product->sku,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $product->price * $quantity,
                ]);
            }

            $order->calculateTotal();

            if ($status === OrderStatus::PROCESSING) {
                $order->markAsProcessing();
            } elseif ($status === OrderStatus::COMPLETED) {
                $order->markAsProcessing();
                $order->markAsCompleted();
            } elseif ($status === OrderStatus::CANCELLED) {
                $order->markAsCancelled();
            }
        }
    }
}

