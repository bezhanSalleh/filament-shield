<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Forms;

use Filament\Forms\Components\Toggle;

class SelectAll extends Toggle
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-shield::forms.select-all';
}
