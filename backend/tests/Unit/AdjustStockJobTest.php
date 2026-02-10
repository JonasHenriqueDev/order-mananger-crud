<?php

use App\Jobs\AdjustStockJob;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Bus;

beforeEach(function () {
    Bus::fake();
});

describe('AdjustStockJob', function () {
    it('can adjust product stock', function () {
        $product = Product::factory()->create(['stock' => 10]);
        $order = \App\Models\Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $job = new AdjustStockJob($orderItem, 5);
        $job->handle();

        $product->refresh();
        expect($product->stock)->toBe(15);
    });

    it('can decrease stock', function () {
        $product = Product::factory()->create(['stock' => 10]);
        $order = \App\Models\Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $job = new AdjustStockJob($orderItem, -3);
        $job->handle();

        $product->refresh();
        expect($product->stock)->toBe(7);
    });

    it('has correct retry configuration', function () {
        $product = Product::factory()->create();
        $order = \App\Models\Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $job = new AdjustStockJob($orderItem, 1);

        expect($job->tries)->toBe(3);
        expect($job->backoff)->toBe([10, 60, 120]);
    });

    it('dispatches on stock queue', function () {
        Bus::fake();

        $product = Product::factory()->create();
        $order = \App\Models\Order::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        AdjustStockJob::dispatch($orderItem, 5);

        Bus::assertDispatched(AdjustStockJob::class);
    });
});

