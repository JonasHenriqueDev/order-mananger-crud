<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class MonitorQueues extends Command
{
    protected $signature = 'queue:monitor';
    protected $description = 'Monitora o status das filas de processamento';

    public function handle()
    {
        $this->line('');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->info('  MONITOR DE FILAS - Order Management System');
        $this->info('═══════════════════════════════════════════════════════════════');
        $this->line('');

        $this->info('Jobs e filas configuradas:');
        $this->line('  • ProcessOrderJob (fila: default) - Processa pedidos');
        $this->line('  • CompleteOrderJob (fila: default) - Completa pedidos');
        $this->line('  • AdjustStockJob (fila: stock) - Ajusta estoque');
        $this->line('');

        $this->info('Para processar filas execute:');
        $this->line('  php artisan queue:work redis --queue=default');
        $this->line('  php artisan queue:work redis --queue=stock');
        $this->line('');

        $this->info('Para monitorar em tempo real execute:');
        $this->line('  php artisan queue:failed');
        $this->line('  php artisan horizon (se Horizon estiver instalado)');
        $this->line('');

        $this->info('═══════════════════════════════════════════════════════════════');
        $this->line('');
    }
}

