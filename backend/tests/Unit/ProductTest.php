<?php

use App\Models\Product;

it('generates sku automatically when not provided', function () {
    $product = Product::factory()->create(['sku' => null]);

    expect($product->sku)->not->toBeNull();
    expect($product->sku)->toStartWith('PROD-');
});

it('generates slug from sku when not provided', function () {
    $product = Product::factory()->create([
        'sku' => 'TEST-SKU-123',
        'slug' => null,
    ]);

    expect($product->slug)->not->toBeNull();
});

it('returns formatted price correctly', function () {
    $product = Product::factory()->create(['price' => 1234.56]);

    expect($product->formatted_price)->toBe('1.234,56');
});

it('adjusts stock correctly with positive value', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $product->adjustStock(5);

    expect($product->fresh()->stock)->toBe(15);
});

it('adjusts stock correctly with negative value', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $product->adjustStock(-3);

    expect($product->fresh()->stock)->toBe(7);
});

it('does not allow negative stock', function () {
    $product = Product::factory()->create(['stock' => 5]);

    $product->adjustStock(-10);

    expect($product->fresh()->stock)->toBe(0);
});

it('checks if product is active', function () {
    $activeProduct = Product::factory()->create(['status' => 'active']);
    $inactiveProduct = Product::factory()->inactive()->create();

    expect($activeProduct->isActive())->toBeTrue();
    expect($inactiveProduct->isActive())->toBeFalse();
});

it('can set and get metadata', function () {
    $product = Product::factory()->create(['metadata' => null]);

    $product->setMeta('color', 'red');

    expect($product->fresh()->getMeta('color'))->toBe('red');
});

it('returns default value for missing metadata key', function () {
    $product = Product::factory()->create(['metadata' => null]);

    expect($product->getMeta('nonexistent', 'default'))->toBe('default');
});

it('can be soft deleted', function () {
    $product = Product::factory()->create();

    $product->delete();

    expect($product->trashed())->toBeTrue();
    expect(Product::withTrashed()->find($product->id))->not->toBeNull();
});

it('can be restored after soft delete', function () {
    $product = Product::factory()->create();
    $product->delete();

    $product->restore();

    expect($product->trashed())->toBeFalse();
    expect(Product::find($product->id))->not->toBeNull();
});

