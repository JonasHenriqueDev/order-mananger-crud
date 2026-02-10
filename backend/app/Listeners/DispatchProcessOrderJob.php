<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Jobs\ProcessOrderJob;

class DispatchProcessOrderJob
{
    public function handle(OrderCreated $event): void
    {
        \Log::info("Despachando ProcessOrderJob para pedido {$event->order->id}");
        ProcessOrderJob::dispatch($event->order);
    }
}

