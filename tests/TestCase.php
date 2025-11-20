<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Forzar entorno de testing
        config(['app.env' => 'testing']);

        // Generar APP_KEY si falta
        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--env' => 'testing']);
        }

        // Reset DB
        Artisan::call('migrate:fresh', [
            '--env' => 'testing'
        ]);

        // Seed inicial
        /*Artisan::call('db:seed', [
            '--env' => 'testing'
        ]);*/
    }

    protected function tearDown(): void
    {
        // Limpia todos los mocks de Mockery
        \Mockery::close();

        parent::tearDown();
    }

}
