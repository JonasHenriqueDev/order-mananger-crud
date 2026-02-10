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
        \Log::info("Completando pedido {$this->order->id}");

        try {
            if ($this->order->status->value !== 'processing') {
                \Log::warning("Pedido {$this->order->id} não está em processamento");
                return;
            }

            $this->order->markAsCompleted();

            \Log::info("Pedido {$this->order->id} completado com sucesso");
        } catch (\Exception $e) {
            \Log::error("Erro ao completar pedido {$this->order->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Falha ao completar pedido {$this->order->id}: {$exception->getMessage()}");

        $this->order->update([
            'notes' => ($this->order->notes ?? '') . "\n[ERRO] Falha ao completar: {$exception->getMessage()}",
        ]);
    }
}

