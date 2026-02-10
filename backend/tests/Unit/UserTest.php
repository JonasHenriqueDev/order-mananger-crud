<?php

use App\Enums\Role;
use App\Models\User;

it('checks if user is admin', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($admin->isAdmin())->toBeTrue();
    expect($user->isAdmin())->toBeFalse();
});

it('checks if user is manager', function () {
    $manager = User::factory()->manager()->create();
    $user = User::factory()->create();

    expect($manager->isManager())->toBeTrue();
    expect($user->isManager())->toBeFalse();
});

it('checks if user has specific role', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->hasRole(Role::ADMIN))->toBeTrue();
    expect($admin->hasRole('admin'))->toBeTrue();
    expect($admin->hasRole(Role::USER))->toBeFalse();
});

it('checks if user has any of the given roles', function () {
    $manager = User::factory()->manager()->create();

    expect($manager->hasAnyRole(['admin', 'manager']))->toBeTrue();
    expect($manager->hasAnyRole(['admin', 'user']))->toBeFalse();
});

it('checks if user is active', function () {
    $activeUser = User::factory()->create(['active' => true]);
    $inactiveUser = User::factory()->inactive()->create();

    expect($activeUser->isActive())->toBeTrue();
    expect($inactiveUser->isActive())->toBeFalse();
});

it('has phones relationship', function () {
    $user = User::factory()->create();

    expect($user->phones())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('has address relationship', function () {
    $user = User::factory()->create();

    expect($user->address())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('can be soft deleted', function () {
    $user = User::factory()->create();

    $user->delete();

    expect($user->trashed())->toBeTrue();
    expect(User::withTrashed()->find($user->id))->not->toBeNull();
});

