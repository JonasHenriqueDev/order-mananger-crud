<?php

use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->manager = User::factory()->manager()->create();
    $this->user = User::factory()->create();
});

describe('Index', function () {
    it('admin can list users', function () {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/users');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'first_name', 'last_name', 'email', 'role'],
                ],
            ]);
    });

    it('manager can list users', function () {
        $response = $this->actingAs($this->manager)->getJson('/api/users');

        $response->assertOk();
    });

    it('regular user cannot list users', function () {
        $response = $this->actingAs($this->user)->getJson('/api/users');

        $response->assertForbidden();
    });

    it('unauthenticated user cannot list users', function () {
        $response = $this->getJson('/api/users');

        $response->assertUnauthorized();
    });
});

describe('Store', function () {
    it('admin can create a user', function () {
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'user',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/users', $userData);

        $response->assertSuccessful()
            ->assertJsonFragment([
                'email' => 'jane@example.com',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);
    });

    it('admin can create a user with phone and address', function () {
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'user',
            'phone' => [
                'country_code' => '+55',
                'number' => '11999999999',
                'type' => 'mobile',
            ],
            'address' => [
                'street' => 'Rua Teste',
                'number' => '123',
                'city' => 'São Paulo',
                'state' => 'SP',
                'postal_code' => '01234-567',
                'country' => 'BR',
            ],
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/users', $userData);

        $response->assertSuccessful();

        $this->assertDatabaseHas('phones', [
            'number' => '11999999999',
        ]);

        $this->assertDatabaseHas('addresses', [
            'street' => 'Rua Teste',
        ]);
    });

    it('validates required fields on create', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/users', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password', 'role']);
    });

    it('validates unique email on create', function () {
        $existingUser = User::factory()->create();

        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => $existingUser->email,
            'password' => 'password123',
            'role' => 'user',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/users', $userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('regular user cannot create users', function () {
        $userData = [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'user',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/users', $userData);

        $response->assertForbidden();
    });
});

describe('Show', function () {
    it('admin can view a user', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/users/{$targetUser->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'email' => $targetUser->email,
            ]);
    });

    it('regular user cannot view other users', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/users/{$targetUser->id}");

        $response->assertForbidden();
    });
});

describe('Update', function () {
    it('admin can update a user', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/users/{$targetUser->id}", [
            'first_name' => 'Updated',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'first_name' => 'Updated',
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'first_name' => 'Updated',
        ]);
    });

    it('admin can update user email to unique value', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/users/{$targetUser->id}", [
            'email' => 'newemail@example.com',
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'email' => 'newemail@example.com',
        ]);
    });

    it('cannot update email to existing email', function () {
        $existingUser = User::factory()->create();
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/users/{$targetUser->id}", [
            'email' => $existingUser->email,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('regular user cannot update other users', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)->putJson("/api/users/{$targetUser->id}", [
            'first_name' => 'Updated',
        ]);

        $response->assertForbidden();
    });
});

describe('Toggle', function () {
    it('admin can toggle user active status', function () {
        $targetUser = User::factory()->create(['active' => true]);

        $response = $this->actingAs($this->admin)->patchJson("/api/users/{$targetUser->id}/toggle");

        $response->assertOk()
            ->assertJsonFragment([
                'active' => false,
            ]);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'active' => false,
        ]);
    });

    it('regular user cannot toggle other users', function () {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)->patchJson("/api/users/{$targetUser->id}/toggle");

        $response->assertForbidden();
    });
});





