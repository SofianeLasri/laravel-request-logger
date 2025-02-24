<?php

namespace Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use function Orchestra\Testbench\artisan;

abstract class TestCase extends BaseTestCase
{
    /**
     * Define database migrations.
     *
     * @return void
     */
    /*protected function defineDatabaseMigrations()
    {
        artisan($this, 'migrate', ['--database' => 'testing']);

        $this->beforeApplicationDestroyed(
            fn() => artisan($this, 'migrate:rollback', ['--database' => 'testing'])
        );
    }*/
}
