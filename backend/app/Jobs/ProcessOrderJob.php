<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;

class ProcessOrderJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [10, 60, 300];
    public $maxExceptions = 1;

    public function __construct(public Order $order)
    {
        $this->onQueue('default');
        $this->delay(now()->addSeconds(5));
    }

    public function middleware(): array
    {
        return [new WithoutOverlapping($this->order->id)];
    }

    public function handle(): void
    {
        \Log::info("Processing order {$this->order->id}");

        try {
            if ($this->order->status->value !== 'pending') {
                \Log::warning("Order {$this->order->id} is not in pending state");
                return;
            }

            $this->order->markAsProcessing();

            \Log::info("Order {$this->order->id} marked as processing");

            foreach ($this->order->items as $item) {
                \Log::info("Validating stock for product {$item->product_id}");

                if ($item->product->stock < 0) {
                    throw new \Exception("Insufficient stock for product {$item->product->name}");
                }
            }

            \Log::info("Order {$this->order->id} processed successfully");
        } catch (\Exception $e) {
            \Log::error("Error processing order {$this->order->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Permanent failure processing order {$this->order->id}: {$exception->getMessage()}");

        $this->order->update([
            'notes' => ($this->order->notes ?? '') . "\n[ERROR] Automatic processing failed: {$exception->getMessage()}",
        ]);
    }
}


