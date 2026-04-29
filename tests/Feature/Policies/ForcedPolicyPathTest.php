<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Commands\Concerns\CanGeneratePolicy;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Facades\Filament;
use Filament\Panel;

beforeEach(function () {
    $panel = Panel::make()->id('admin')->default();
    Filament::setCurrentPanel($panel);
});

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('forces policies into base path when enabled', function () {
    config()->set('filament-shield.policies.path', app_path('Policies'));
    config()->set('filament-shield.policies.panel_path', false);
    config()->set('filament-shield.policies.force_path', true);

    $generator = new class
    {
        use CanGeneratePolicy;

        public function pathFor(string $resourceFqcn): string
        {
            $entity = [
                'resourceFqcn' => $resourceFqcn,
                'model' => class_basename($resourceFqcn::getModel()),
                'modelFqcn' => (string) str($resourceFqcn::getModel()),
            ];

            return $this->generatePolicyPath($entity);
        }
    };

    $path = $generator->pathFor(RoleResource::class);

    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'RolePolicy.php');
});

it('forces policies into panel subdirectory when enabled', function () {
    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.path', app_path('Policies'));
    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.force_path', true);

    $generator = new class
    {
        use CanGeneratePolicy;

        public function pathFor(string $resourceFqcn): string
        {
            $entity = [
                'resourceFqcn' => $resourceFqcn,
                'model' => class_basename($resourceFqcn::getModel()),
                'modelFqcn' => (string) str($resourceFqcn::getModel()),
            ];

            return $this->generatePolicyPath($entity);
        }
    };

    $path = $generator->pathFor(RoleResource::class);

    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'System' . DIRECTORY_SEPARATOR . 'RolePolicy.php');
});
