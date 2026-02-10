<?php

use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->manager = User::factory()->manager()->create();
    $this->user = User::factory()->create();
});

describe('Index', function () {
    it('admin can list products', function () {
        Product::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'sku', 'price', 'stock', 'status'],
                ],
            ]);
    });

    it('manager can list products', function () {
        Product::factory()->count(3)->create();

        $response = $this->actingAs($this->manager)->getJson('/api/products');

        $response->assertOk();
    });

    it('regular user cannot list products', function () {
        $response = $this->actingAs($this->user)->getJson('/api/products');

        $response->assertForbidden();
    });

    it('unauthenticated user cannot list products', function () {
        $response = $this->getJson('/api/products');

        $response->assertUnauthorized();
    });

    it('only shows active products in listing', function () {
        Product::factory()->count(3)->create(['status' => 'active']);
        Product::factory()->count(2)->inactive()->create();

        $response = $this->actingAs($this->admin)->getJson('/api/products');

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(3);
    });
});

describe('Store', function () {
    it('admin can create a product', function () {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 50,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/products', $productData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Test Product',
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
        ]);
    });

    it('auto-generates sku and slug if not provided', function () {
        $productData = [
            'name' => 'Auto SKU Product',
            'price' => 50.00,
            'stock' => 10,
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/products', $productData);

        $response->assertStatus(201);

        $product = Product::where('name', 'Auto SKU Product')->first();
        expect($product->sku)->not->toBeNull();
        expect($product->slug)->not->toBeNull();
    });

    it('validates required fields on create', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    });

    it('validates unique sku on create', function () {
        $existingProduct = Product::factory()->create(['sku' => 'UNIQUE-SKU']);

        $productData = [
            'name' => 'New Product',
            'price' => 100,
            'sku' => 'UNIQUE-SKU',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/products', $productData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    });

    it('regular user cannot create products', function () {
        $productData = [
            'name' => 'Test Product',
            'price' => 99.99,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/products', $productData);

        $response->assertForbidden();
    });
});

describe('Show', function () {
    it('admin can view a product', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/products/{$product->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $product->name,
            ]);
    });

    it('manager can view a product', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->manager)->getJson("/api/products/{$product->id}");

        $response->assertOk();
    });

    it('regular user cannot view products', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/products/{$product->id}");

        $response->assertForbidden();
    });
});

describe('Update', function () {
    it('admin can update a product', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/products/{$product->id}", [
            'name' => 'Updated Product Name',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'name' => 'Updated Product Name',
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
        ]);
    });

    it('can update product price', function () {
        $product = Product::factory()->create(['price' => 100.00]);

        $response = $this->actingAs($this->admin)->putJson("/api/products/{$product->id}", [
            'price' => 150.50,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'price' => 150.50,
        ]);
    });

    it('can update product stock', function () {
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($this->admin)->putJson("/api/products/{$product->id}", [
            'stock' => 25,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 25,
        ]);
    });

    it('cannot update sku to existing sku', function () {
        $existingProduct = Product::factory()->create(['sku' => 'EXISTING-SKU']);
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/products/{$product->id}", [
            'sku' => 'EXISTING-SKU',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['sku']);
    });

    it('regular user cannot update products', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/products/{$product->id}", [
            'name' => 'Updated',
        ]);

        $response->assertForbidden();
    });
});

describe('Delete', function () {
    it('admin can delete a product (soft delete)', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/api/products/{$product->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('products', [
            'id' => $product->id,
        ]);
    });

    it('regular user cannot delete products', function () {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/products/{$product->id}");

        $response->assertForbidden();
    });
});

describe('Restore', function () {
    it('admin can restore a soft-deleted product', function () {
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->actingAs($this->admin)->postJson("/api/products/{$product->id}/restore");

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $product->name,
            ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null,
        ]);
    });

    it('regular user cannot restore products', function () {
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->actingAs($this->user)->postJson("/api/products/{$product->id}/restore");

        $response->assertForbidden();
    });
});

describe('Force Delete', function () {
    it('admin can permanently delete a product', function () {
        $product = Product::factory()->create();
        $productId = $product->id;
        $product->delete();

        $response = $this->actingAs($this->admin)->deleteJson("/api/products/{$productId}/force");

        $response->assertNoContent();

        $this->assertDatabaseMissing('products', [
            'id' => $productId,
        ]);
    });

    it('regular user cannot force delete products', function () {
        $product = Product::factory()->create();
        $product->delete();

        $response = $this->actingAs($this->user)->deleteJson("/api/products/{$product->id}/force");

        $response->assertForbidden();
    });
});


