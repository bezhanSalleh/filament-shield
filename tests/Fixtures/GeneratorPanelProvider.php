<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures;

use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\AuthorResource;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\EditorResource;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\ItemResource;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\ReporterResource;
use Filament\Panel;
use Filament\PanelProvider;

class GeneratorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('generator')
            ->path('generator')
            ->resources([
                AuthorResource::class,
                EditorResource::class,
                ItemResource::class,
                ReporterResource::class,
            ]);
    }
}
