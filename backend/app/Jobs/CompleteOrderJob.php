<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CompleteOrderJob implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = [30, 60, 300];

    public function __construct(public Order $order)
    {
        $this->onQueue('default');
        $this->delay(now()->addMinutes(2));
    }

    public function handle(): void
    {
        \Log::info("Completing order {$this->order->id}");

        try {
            if ($this->order->status->value !== 'processing') {
                \Log::warning("Order {$this->order->id} is not in processing state");
                return;
            }

            $this->order->markAsCompleted();

            \Log::info("Order {$this->order->id} completed successfully");
        } catch (\Exception $e) {
            \Log::error("Error completing order {$this->order->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Failed to complete order {$this->order->id}: {$exception->getMessage()}");

        $this->order->update([
            'notes' => ($this->order->notes ?? '') . "\n[ERROR] Failed to complete: {$exception->getMessage()}",
        ]);
    }
}

