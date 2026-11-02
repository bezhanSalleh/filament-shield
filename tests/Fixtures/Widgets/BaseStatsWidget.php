<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Widget;

abstract class BaseStatsWidget extends Widget
{
    use HasWidgetShield;

    protected string $view = 'filament-widgets::widget';
}
