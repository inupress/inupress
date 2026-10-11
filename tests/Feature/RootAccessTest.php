<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('lets a root user into the admin panel', function () {
    $this->actingAs(User::factory()->root()->create())
        ->get('/admin')
        ->assertOk();
});

it('forbids a regular user from the admin panel', function () {
    $this->actingAs(User::factory()->create())
        ->get('/admin')
        ->assertForbidden();
});

it('does not make a user root through mass assignment', function () {
    $user = User::create(['name' => 'X', 'email' => 'x@example.com', 'password' => 'secret-pass-123', 'is_root' => true]);

    expect($user->fresh()->is_root)->toBeFalse();
});

it('creates a root user with inupress:make-root', function () {
    $this->artisan('inupress:make-root', ['--name' => 'Admin', '--email' => 'admin@example.com', '--password' => 'secret-pass-123'])
        ->assertSuccessful();

    $user = User::where('email', 'admin@example.com')->firstOrFail();

    expect($user->is_root)->toBeTrue()
        ->and($user->password)->not->toBe('secret-pass-123');
});

it('refuses a weak password and a duplicate email', function () {
    $this->artisan('inupress:make-root', ['--name' => 'A', '--email' => 'a@example.com', '--password' => 'short'])
        ->assertFailed();

    User::factory()->create(['email' => 'dup@example.com']);

    $this->artisan('inupress:make-root', ['--name' => 'B', '--email' => 'dup@example.com', '--password' => 'secret-pass-123'])
        ->assertFailed();

    expect(User::where('email', 'a@example.com')->exists())->toBeFalse();
});
