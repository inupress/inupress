<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:install {--fresh}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install InuPress CMS';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            Artisan::call('migrate:fresh');
        } else {
            Artisan::call('migrate');
        }
        Artisan::call('make:filament-user', [
            '--name' => 'root',
            '--email' => 'root@root.root',
            '--password' => 'password',
        ]);
    }
}
