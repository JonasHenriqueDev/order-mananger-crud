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
        \Log::info("Ajustando estoque para produto {$this->orderItem->product_id}");

        try {
            $this->orderItem->product->adjustStock($this->quantity);

            \Log::info("Estoque ajustado com sucesso. Produto: {$this->orderItem->product_id}, Quantidade: {$this->quantity}");
        } catch (\Exception $e) {
            \Log::error("Erro ao ajustar estoque: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Falha ao ajustar estoque após 3 tentativas: {$exception->getMessage()}");
    }
}

