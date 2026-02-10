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
        \Log::info("Processando pedido {$this->order->id}");

        try {
            if ($this->order->status->value !== 'pending') {
                \Log::warning("Pedido {$this->order->id} não está em estado pendente");
                return;
            }

            $this->order->markAsProcessing();

            \Log::info("Pedido {$this->order->id} marcado como processando");

            foreach ($this->order->items as $item) {
                \Log::info("Validando estoque do produto {$item->product_id}");

                if ($item->product->stock < 0) {
                    throw new \Exception("Estoque insuficiente para produto {$item->product->name}");
                }
            }

            \Log::info("Pedido {$this->order->id} processado com sucesso");
        } catch (\Exception $e) {
            \Log::error("Erro ao processar pedido {$this->order->id}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error("Falha permanente ao processar pedido {$this->order->id}: {$exception->getMessage()}");

        $this->order->update([
            'notes' => ($this->order->notes ?? '') . "\n[ERRO] Falha no processamento automático: {$exception->getMessage()}",
        ]);
    }
}


