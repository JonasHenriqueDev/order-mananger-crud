<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public string $previousStatus,
        public string $newStatus
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('orders'),
        ];
    }
}

