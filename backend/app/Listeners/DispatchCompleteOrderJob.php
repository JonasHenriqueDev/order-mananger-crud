<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Jobs\CompleteOrderJob;

class DispatchCompleteOrderJob
{
    public function handle(OrderStatusChanged $event): void
    {
        if ($event->newStatus === 'processing' && $event->previousStatus === 'pending') {
            \Log::info("Despachando CompleteOrderJob para pedido {$event->order->id}");
            CompleteOrderJob::dispatch($event->order);
        }
    }
}

