<?php

use App\Models\User;

beforeEach(function () {
    $this->userData = [
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
});

describe('Register', function () {
    it('can register a new user', function () {
        $response = $this->postJson('/api/register', $this->userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => ['id', 'first_name', 'last_name', 'email', 'role'],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);
    });

    it('validates required fields on register', function () {
        $response = $this->postJson('/api/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['first_name', 'last_name', 'email', 'password']);
    });

    it('validates unique email on register', function () {
        User::factory()->create(['email' => 'john@example.com']);

        $response = $this->postJson('/api/register', $this->userData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('validates password confirmation on register', function () {
        $data = $this->userData;
        $data['password_confirmation'] = 'differentpassword';

        $response = $this->postJson('/api/register', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    });
});

describe('Login', function () {
    it('can login with valid credentials', function () {
        $user = User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token',
                'user',
            ]);
    });

    it('cannot login with invalid password', function () {
        User::factory()->create([
            'email' => 'john@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'john@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('cannot login with non-existent email', function () {
        $response = $this->postJson('/api/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    });

    it('validates required fields on login', function () {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    });
});

describe('Me', function () {
    it('can get authenticated user', function () {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/me');

        $response->assertOk()
            ->assertJsonFragment([
                'email' => $user->email,
            ]);
    });

    it('cannot access me endpoint without authentication', function () {
        $response = $this->getJson('/api/me');

        $response->assertUnauthorized();
    });
});

