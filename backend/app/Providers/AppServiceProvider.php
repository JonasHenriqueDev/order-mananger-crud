<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderStatusChanged;
use App\Listeners\DispatchProcessOrderJob;
use App\Listeners\DispatchCompleteOrderJob;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            DispatchProcessOrderJob::class,
        ],
        OrderStatusChanged::class => [
            DispatchCompleteOrderJob::class,
        ],
    ];

    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}
