<?php

use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Filament\Support\SupportServiceProvider;
use Orchestra\Testbench\Foundation\Application;
use Orchestra\Testbench\Concerns\CreatesApplication;
use BezhanSalleh\FilamentShield\FilamentShieldServiceProvider;

$basePathLocator = new class () {
    use CreatesApplication;
};

$app = (new Application($basePathLocator::applicationBasePath()))
    ->configure([
        'enables_package_discoveries' => true,
    ])
    ->createApplication();

$app->register(LivewireServiceProvider::class);
$app->register(FilamentServiceProvider::class);
$app->register(SupportServiceProvider::class);

return $app;
