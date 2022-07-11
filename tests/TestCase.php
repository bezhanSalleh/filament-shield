<?php

namespace BezhanSalleh\FilamentShield\Tests;

use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'BezhanSalleh\\FilamentShield\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentShieldServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        
        $migration = include __DIR__.'/../database/migrations/create_filament-shield_table.php.stub';
        $migration->up();

    }
}
