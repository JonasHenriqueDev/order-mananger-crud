<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->manager = User::factory()->manager()->create();
    $this->user = User::factory()->create();
});

describe('Index', function () {
    it('admin can list orders', function () {
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/orders');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'order_number', 'status', 'total', 'user', 'items'],
                ],
            ]);
    });

    it('manager can list orders', function () {
        Order::factory()->count(5)->create();

        $response = $this->actingAs($this->manager)->getJson('/api/orders');

        $response->assertOk();
    });

    it('regular user cannot list all orders', function () {
        $response = $this->actingAs($this->user)->getJson('/api/orders');

        $response->assertForbidden();
    });

    it('can filter orders by status', function () {
        Queue::fake();

        Order::factory()->pending()->count(2)->create();
        Order::factory()->completed()->count(3)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/orders?status=pending');

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(2);
    });

    it('can filter orders by user', function () {
        $targetUser = User::factory()->create();
        Order::factory()->count(3)->create(['user_id' => $targetUser->id]);
        Order::factory()->count(2)->create();

        $response = $this->actingAs($this->admin)->getJson("/api/orders?user_id={$targetUser->id}");

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(3);
    });
});

describe('Store', function () {
    it('admin can create an order with multiple items', function () {
        $targetUser = User::factory()->create();
        $product1 = Product::factory()->create(['price' => 100, 'stock' => 10, 'status' => 'active']);
        $product2 = Product::factory()->create(['price' => 50, 'stock' => 5, 'status' => 'active']);

        $orderData = [
            'user_id' => $targetUser->id,
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2],
                ['product_id' => $product2->id, 'quantity' => 1],
            ],
            'tax' => 10,
            'discount' => 5,
            'notes' => 'Test order',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/orders', $orderData);

        $response->assertSuccessful()
            ->assertJsonFragment([
                'total' => '255.00', // (100*2 + 50*1) + 10 - 5
            ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $targetUser->id,
            'notes' => 'Test order',
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        // Check stock was reduced
        expect($product1->fresh()->stock)->toBe(8);
        expect($product2->fresh()->stock)->toBe(4);
    });

    it('validates stock availability when creating order', function () {
        $product = Product::factory()->create(['stock' => 2, 'status' => 'active']);

        $orderData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 5],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.quantity']);
    });

    it('validates product is active when creating order', function () {
        $product = Product::factory()->create(['stock' => 10, 'status' => 'inactive']);

        $orderData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/orders', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items.0.product_id']);
    });

    it('validates required fields on create', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['items']);
    });

    it('auto-generates order number', function () {
        $product = Product::factory()->create(['stock' => 10, 'status' => 'active']);

        $orderData = [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1],
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/orders', $orderData);

        $response->assertSuccessful();
        expect($response->json('data.order_number'))->toStartWith('ORD-');
    });
});

describe('Show', function () {
    it('admin can view an order', function () {
        $order = Order::factory()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/orders/{$order->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'order_number' => $order->order_number,
            ])
            ->assertJsonStructure([
                'data' => ['items' => []],
            ]);
    });

    it('regular user cannot view orders', function () {
        $order = Order::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/orders/{$order->id}");

        $response->assertForbidden();
    });
});

describe('Update', function () {
    it('admin can update order status to processing', function () {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/orders/{$order->id}", [
            'status' => 'processing',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'status' => 'processing',
            ]);

        expect($order->fresh()->processed_at)->not->toBeNull();
    });

    it('admin can update order status to completed', function () {
        $order = Order::factory()->processing()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/orders/{$order->id}", [
            'status' => 'completed',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'status' => 'completed',
            ]);

        expect($order->fresh()->completed_at)->not->toBeNull();
    });

    it('admin can update order tax and discount', function () {
        $order = Order::factory()->create(['tax' => 0, 'discount' => 0]);

        $response = $this->actingAs($this->admin)->putJson("/api/orders/{$order->id}", [
            'tax' => 15,
            'discount' => 10,
        ]);

        $response->assertOk();

        $order->refresh();
        expect($order->tax)->toBe('15.00');
        expect($order->discount)->toBe('10.00');
    });

    it('cannot cancel completed order', function () {
        $order = Order::factory()->completed()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/orders/{$order->id}", [
            'status' => 'cancelled',
        ]);

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Este pedido não pode ser cancelado.',
            ]);
    });

    it('restores stock when order is cancelled', function () {
        Queue::fake();

        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->pending()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        // Reduce stock
        $product->adjustStock(-3);
        expect($product->fresh()->stock)->toBe(7);

        // Cancel order
        $response = $this->actingAs($this->admin)->putJson("/api/orders/{$order->id}", [
            'status' => 'cancelled',
        ]);

        $response->assertOk();

        // Stock should be restored
        expect($product->fresh()->stock)->toBe(10);
    });
});

describe('Delete', function () {
    it('admin can delete pending order', function () {
        Queue::fake();

        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->pending()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $product->adjustStock(-2);
        expect($product->fresh()->stock)->toBe(8);

        $response = $this->actingAs($this->admin)->deleteJson("/api/orders/{$order->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('orders', [
            'id' => $order->id,
        ]);

        // Stock should be restored
        expect($product->fresh()->stock)->toBe(10);
    });

    it('cannot delete non-pending order', function () {
        $order = Order::factory()->completed()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Apenas pedidos pendentes podem ser excluídos.',
            ]);
    });

    it('regular user cannot delete orders', function () {
        $order = Order::factory()->pending()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/orders/{$order->id}");

        $response->assertForbidden();
    });
});

describe('Cancel', function () {
    it('admin can cancel pending order', function () {
        Queue::fake();

        $product = Product::factory()->create(['stock' => 10]);
        $order = Order::factory()->pending()->create();
        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->admin)->postJson("/api/orders/{$order->id}/cancel");

        $response->assertOk()
            ->assertJsonFragment([
                'status' => 'cancelled',
            ]);

        expect($order->fresh()->cancelled_at)->not->toBeNull();
    });

    it('cannot cancel completed order', function () {
        $order = Order::factory()->completed()->create();

        $response = $this->actingAs($this->admin)->postJson("/api/orders/{$order->id}/cancel");

        $response->assertStatus(422);
    });
});

describe('My Orders', function () {
    it('user can view their own orders', function () {
        Order::factory()->count(3)->create(['user_id' => $this->user->id]);
        Order::factory()->count(2)->create(); // Other user's orders

        $response = $this->actingAs($this->user)->getJson('/api/my-orders');

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(3);
    });

    it('user can filter their orders by status', function () {
        Queue::fake();

        Order::factory()->pending()->count(2)->create(['user_id' => $this->user->id]);
        Order::factory()->completed()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user)->getJson('/api/my-orders?status=pending');

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(2);
    });

    it('unauthenticated user cannot access my orders', function () {
        $response = $this->getJson('/api/my-orders');

        $response->assertUnauthorized();
    });
});

describe('Order Calculations', function () {
    it('correctly calculates order total', function () {
        $product1 = Product::factory()->create(['price' => 100, 'stock' => 10, 'status' => 'active']);
        $product2 = Product::factory()->create(['price' => 75.50, 'stock' => 10, 'status' => 'active']);

        $orderData = [
            'items' => [
                ['product_id' => $product1->id, 'quantity' => 2], // 200
                ['product_id' => $product2->id, 'quantity' => 3], // 226.50
            ],
            'tax' => 25.50,
            'discount' => 10.00,
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/orders', $orderData);

        $response->assertSuccessful();

        // subtotal: 426.50, tax: 25.50, discount: 10.00 = 442.00
        $order = Order::latest()->first();
        expect($order->subtotal)->toBe('426.50');
        expect($order->total)->toBe('442.00');
    });
});

