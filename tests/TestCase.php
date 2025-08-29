<?php

namespace BezhanSalleh\FilamentShield\Tests;

use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;
use Filament\FilamentServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'BezhanSalleh\\FilamentShield\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            FilamentServiceProvider::class,
            LivewireServiceProvider::class,
            FilamentShieldServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('permission.models.permission', Permission::class);
        config()->set('permission.models.role', Role::class);
        config()->set('permission.cache.key', 'spatie.permission.cache');

        // $migration = include __DIR__.'/../database/migrations/create_filament_shield_settings_table.php.stub';
        // $migration->up();
    }
}
