<?php

use App\Models\Address;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->manager = User::factory()->manager()->create();
    $this->user = User::factory()->create();
});

describe('Index', function () {
    it('admin can list addresses', function () {
        Address::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/addresses');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'street', 'city', 'state', 'postal_code'],
                ],
            ]);
    });

    it('regular user cannot list addresses', function () {
        $response = $this->actingAs($this->user)->getJson('/api/addresses');

        $response->assertForbidden();
    });
});

describe('Store', function () {
    it('admin can create an address', function () {
        $targetUser = User::factory()->create();

        $addressData = [
            'user_id' => $targetUser->id,
            'street' => 'Rua Teste',
            'number' => '123',
            'city' => 'São Paulo',
            'state' => 'SP',
            'postal_code' => '01234-567',
            'country' => 'BR',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/addresses', $addressData);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'street' => 'Rua Teste',
            ]);

        $this->assertDatabaseHas('addresses', [
            'street' => 'Rua Teste',
        ]);
    });

    it('validates required fields on create', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/addresses', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'street', 'city', 'state', 'postal_code', 'country']);
    });
});

describe('Show', function () {
    it('admin can view an address', function () {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/addresses/{$address->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'street' => $address->street,
            ]);
    });

    it('regular user cannot view addresses', function () {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/addresses/{$address->id}");

        $response->assertForbidden();
    });
});

describe('Update', function () {
    it('admin can update an address', function () {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/addresses/{$address->id}", [
            'street' => 'Rua Atualizada',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'street' => 'Rua Atualizada',
            ]);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'street' => 'Rua Atualizada',
        ]);
    });
});

describe('Delete', function () {
    it('admin can delete an address', function () {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/api/addresses/{$address->id}");

        $response->assertNoContent();

        $this->assertSoftDeleted('addresses', [
            'id' => $address->id,
        ]);
    });

    it('regular user cannot delete addresses', function () {
        $address = Address::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/addresses/{$address->id}");

        $response->assertForbidden();
    });
});

describe('User Addresses', function () {
    it('admin can list addresses for a specific user', function () {
        $targetUser = User::factory()->create();
        Address::factory()->count(2)->create(['user_id' => $targetUser->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/users/{$targetUser->id}/addresses");

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(2);
    });
});

