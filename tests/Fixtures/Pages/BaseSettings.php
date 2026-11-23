<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;

abstract class BaseSettings extends Page
{
    use HasPageShield;

    protected string $view = 'filament-panels::pages.dashboard';
}
