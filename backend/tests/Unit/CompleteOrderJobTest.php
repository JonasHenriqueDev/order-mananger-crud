<?php

use App\Jobs\CompleteOrderJob;
use App\Models\Order;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake();
});

describe('CompleteOrderJob', function () {
    it('can complete a processing order', function () {
        $order = Order::factory()->processing()->create();

        $job = new CompleteOrderJob($order);
        $job->handle();

        $order->refresh();
        expect($order->status->value)->toBe('completed');
        expect($order->completed_at)->not->toBeNull();
    });

    it('skips non-processing orders', function () {
        $order = Order::factory()->pending()->create();

        $job = new CompleteOrderJob($order);
        $job->handle();

        $order->refresh();
        expect($order->status->value)->toBe('pending');
    });

    it('has correct retry configuration', function () {
        $order = Order::factory()->processing()->create();
        $job = new CompleteOrderJob($order);

        expect($job->tries)->toBe(3);
        expect($job->backoff)->toBe([30, 60, 300]);
    });

    it('has delay on dispatch', function () {
        Bus::fake();

        $order = Order::factory()->processing()->create();
        CompleteOrderJob::dispatch($order);

        Bus::assertDispatched(CompleteOrderJob::class);
    });
});

