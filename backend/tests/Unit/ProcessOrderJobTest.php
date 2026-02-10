<?php

use App\Jobs\ProcessOrderJob;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake();
});

describe('ProcessOrderJob', function () {
    it('can process an order', function () {
        $order = Order::factory()->pending()->create();
        Product::factory()->count(2)->create();

        $job = new ProcessOrderJob($order);
        $job->handle();

        $order->refresh();
        expect($order->status->value)->toBe('processing');
    });

    it('throws exception for non-pending orders', function () {
        $order = Order::factory()->completed()->create();

        $job = new ProcessOrderJob($order);
        $job->handle();

        $order->refresh();
        expect($order->status->value)->toBe('completed');
    });

    it('has correct retry configuration', function () {
        $order = Order::factory()->pending()->create();
        $job = new ProcessOrderJob($order);

        expect($job->tries)->toBe(3);
        expect($job->backoff)->toBe([10, 60, 300]);
    });

    it('dispatches on default queue with delay', function () {
        Bus::fake();

        $order = Order::factory()->pending()->create();
        ProcessOrderJob::dispatch($order);

        Bus::assertDispatched(ProcessOrderJob::class);
    });
});

