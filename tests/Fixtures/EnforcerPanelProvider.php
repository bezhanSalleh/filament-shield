<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures;

use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\ArticleResource;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\CommentResource;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Resources\DraftResource;
use Filament\Panel;
use Filament\PanelProvider;

class EnforcerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('enforcer')
            ->path('enforcer')
            ->resources([
                ArticleResource::class,
                CommentResource::class,
                DraftResource::class,
            ]);
    }
}
