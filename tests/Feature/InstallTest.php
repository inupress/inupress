<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a root user from options without a terminal', function () {
    $this->artisan('inupress:install', ['--email' => 'admin@example.com', '--password' => 'secret-pass-123', '--no-interaction' => true])
        ->assertSuccessful();

    $user = User::where('email', 'admin@example.com')->firstOrFail();

    expect($user->is_root)->toBeTrue()->and($user->name)->toBe('Admin');
});

it('generates a password and shows it once when none is given', function () {
    $this->artisan('inupress:install', ['--email' => 'admin@example.com', '--no-interaction' => true])
        ->expectsOutputToContain('Generated password for admin@example.com')
        ->assertSuccessful();

    expect(User::where('email', 'admin@example.com')->firstOrFail()->is_root)->toBeTrue();
});

it('refuses to guess an email without a terminal', function () {
    $this->artisan('inupress:install', ['--no-interaction' => true])->assertFailed();

    expect(User::count())->toBe(0);
});

it('is idempotent: a second run keeps the existing root user', function () {
    $this->artisan('inupress:install', ['--email' => 'admin@example.com', '--password' => 'secret-pass-123', '--no-interaction' => true])->assertSuccessful();
    $this->artisan('inupress:install', ['--no-interaction' => true])->assertSuccessful();

    expect(User::count())->toBe(1);
});

it('does not generate a password when one is passed', function () {
    $this->artisan('inupress:install', ['--email' => 'a@example.com', '--password' => 'secret-pass-123', '--no-interaction' => true])
        ->doesntExpectOutputToContain('Generated password')
        ->assertSuccessful();
});
