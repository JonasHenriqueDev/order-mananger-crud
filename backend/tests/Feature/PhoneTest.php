<?php

use App\Models\Phone;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->manager = User::factory()->manager()->create();
    $this->user = User::factory()->create();
});

describe('Index', function () {
    it('admin can list phones', function () {
        Phone::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->getJson('/api/phones');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'country_code', 'number', 'type'],
                ],
            ]);
    });

    it('regular user cannot list phones', function () {
        $response = $this->actingAs($this->user)->getJson('/api/phones');

        $response->assertForbidden();
    });
});

describe('Store', function () {
    it('admin can create a phone', function () {
        $targetUser = User::factory()->create();

        $phoneData = [
            'user_id' => $targetUser->id,
            'country_code' => '+55',
            'number' => '11999888777',
            'type' => 'mobile',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/phones', $phoneData);

        $response->assertSuccessful()
            ->assertJsonFragment([
                'number' => '11999888777',
            ]);

        $this->assertDatabaseHas('phones', [
            'number' => '11999888777',
        ]);
    });

    it('validates required fields on create', function () {
        $response = $this->actingAs($this->admin)->postJson('/api/phones', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['user_id', 'country_code', 'number', 'type']);
    });
});

describe('Show', function () {
    it('admin can view a phone', function () {
        $phone = Phone::factory()->create();

        $response = $this->actingAs($this->admin)->getJson("/api/phones/{$phone->id}");

        $response->assertOk()
            ->assertJsonFragment([
                'number' => $phone->number,
            ]);
    });

    it('regular user cannot view phones', function () {
        $phone = Phone::factory()->create();

        $response = $this->actingAs($this->user)->getJson("/api/phones/{$phone->id}");

        $response->assertForbidden();
    });
});

describe('Update', function () {
    it('admin can update a phone', function () {
        $phone = Phone::factory()->create();

        $response = $this->actingAs($this->admin)->putJson("/api/phones/{$phone->id}", [
            'number' => '11888777666',
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'number' => '11888777666',
            ]);

        $this->assertDatabaseHas('phones', [
            'id' => $phone->id,
            'number' => '11888777666',
        ]);
    });
});

describe('Delete', function () {
    it('admin can delete a phone', function () {
        $phone = Phone::factory()->create();

        $response = $this->actingAs($this->admin)->deleteJson("/api/phones/{$phone->id}");

        $response->assertOk();

        $this->assertSoftDeleted('phones', [
            'id' => $phone->id,
        ]);
    });

    it('regular user cannot delete phones', function () {
        $phone = Phone::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/phones/{$phone->id}");

        $response->assertForbidden();
    });
});

describe('User Phones', function () {
    it('admin can list phones for a specific user', function () {
        $targetUser = User::factory()->create();
        Phone::factory()->count(3)->create(['user_id' => $targetUser->id]);

        $response = $this->actingAs($this->admin)->getJson("/api/users/{$targetUser->id}/phones");

        $response->assertOk();
        expect($response->json('data'))->toHaveCount(3);
    });
});



