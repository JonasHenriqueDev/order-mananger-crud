<?php

namespace App\Jobs;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AdjustStockJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [10, 60, 120];

    public function __construct(
        public OrderItem $orderItem,
        public int $quantity
    ) {
        $this->onQueue('stock');
    }

    public function handle(): void
    {
        \Log::info("Adjusting stock for product {$this->orderItem->product_id}");

        try {
            $this->orderItem->product->adjustStock($this->quantity);

            \Log::info("Stock adjusted successfully. Product: {$this->orderItem->product_id}, Quantity: {$this->quantity}");
        } catch (\Exception $e) {
            \Log::error("Error adjusting stock: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Failed to adjust stock after 3 attempts: {$exception->getMessage()}");
    }
}

