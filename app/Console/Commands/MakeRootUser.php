<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

final class MakeRootUser extends Command
{
    protected $signature = 'inupress:make-root
        {--name= : Display name}
        {--email= : Login email}
        {--password= : Password (prefer the prompt: a password in the command line stays in shell history)}';

    protected $description = 'Create a root user who can sign in to the admin panel';

    public function handle(): int
    {
        $name = (string) ($this->option('name') ?? text('Name', required: true));
        $email = (string) ($this->option('email') ?? text('Email', required: true));
        $secret = (string) ($this->option('password') ?? password('Password (min 8 characters)', required: true));

        $validator = Validator::make(
            ['name' => $name, 'email' => $email, 'password' => $secret],
            ['name' => 'required|string|max:255', 'email' => 'required|email|max:255|unique:users,email', 'password' => 'required|string|min:8'],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->components->error($error);
            }

            return self::FAILURE;
        }

        $user = new User(['name' => $name, 'email' => $email, 'password' => Hash::make($secret)]);
        // is_root is deliberately not mass assignable.
        $user->is_root = true;
        $user->save();

        $this->components->info("Root user [$email] created. Sign in at ".url('/admin'));

        return self::SUCCESS;
    }
}
