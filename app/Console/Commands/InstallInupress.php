<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

final class InstallInupress extends Command
{
    protected $signature = 'inupress:install
        {--name= : Administrator name (default: Admin)}
        {--email= : Administrator email (required without a terminal)}
        {--password= : Administrator password (generated and shown once when omitted without a terminal)}';

    protected $description = 'Finish the installation: .env, application key, database, migrations and the first administrator';

    public function handle(): int
    {
        $this->prepareEnvironment();

        $this->components->info('Running migrations');

        if ($this->call('migrate', ['--force' => true]) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if (User::where('is_root', true)->exists() && ! $this->option('email')) {
            $this->components->info('A root user already exists, nothing more to do. Create another with inupress:make-root.');

            return self::SUCCESS;
        }

        return $this->createRootUser();
    }

    private function prepareEnvironment(): void
    {
        // Tests must never touch the real .env or the real database file.
        if ($this->laravel->runningUnitTests()) {
            return;
        }

        if (! is_file(base_path('.env')) && is_file(base_path('.env.example'))) {
            copy(base_path('.env.example'), base_path('.env'));
            $this->components->info('Created .env from .env.example');
        }

        if (blank(config('app.key'))) {
            $this->call('key:generate', ['--force' => true]);
        }

        if (config('database.default') === 'sqlite') {
            $file = (string) config('database.connections.sqlite.database');

            if ($file !== '' && $file !== ':memory:' && ! is_file($file)) {
                touch($file);
                $this->components->info('Created the SQLite database file');
            }
        }
    }

    private function createRootUser(): int
    {
        $interactive = $this->input->isInteractive();
        $email = $this->option('email');
        $password = $this->option('password');
        $generated = false;

        if (! $interactive && ! $email) {
            $this->components->error('Without a terminal, pass --email (and optionally --password).');

            return self::FAILURE;
        }

        if (! $interactive && ! $password) {
            $password = Str::password(16);
            $generated = true;
        }

        $arguments = array_filter([
            '--name' => $this->option('name') ?: ($interactive ? null : 'Admin'),
            '--email' => $email,
            '--password' => $password,
        ], fn ($value) => $value !== null && $value !== '');

        if ($this->call('inupress:make-root', $arguments) !== self::SUCCESS) {
            return self::FAILURE;
        }

        if ($generated) {
            $this->components->warn("Generated password for {$email}: {$password}");
            $this->components->warn('It is shown only once. Change it after signing in.');
        }

        $this->components->info('Done. Start the server with: php artisan serve');

        return self::SUCCESS;
    }
}
