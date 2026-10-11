<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // A cached config ignores phpunit.xml, so the app would run with APP_ENV=local and the
        // dev database. Livewire/Filament form tests then fail in confusing ways.
        if ($this->app->configurationIsCached() && ! $this->app->runningUnitTests()) {
            $this->fail('Config is cached for APP_ENV='.$this->app->environment().', so phpunit.xml is ignored. Run `php artisan config:clear` (or `optimize:clear`) before the tests.');
        }
    }
}
