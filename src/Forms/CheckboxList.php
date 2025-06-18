<?php

namespace BezhanSalleh\FilamentShield\Forms;

use Filament\Forms\Components\CheckboxList as FilamentCheckboxList;

class CheckboxList extends FilamentCheckboxList
{
    /**
     * @var view-string
     */
    protected string $view = 'filament-shield::forms.checkbox-list';
}
