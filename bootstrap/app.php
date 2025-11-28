<?php

declare(strict_types=1);

use Filament\FilamentServiceProvider;
use Filament\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Orchestra\Testbench\Foundation\Application;

$basePathLocator = new class
{
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
