<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRole extends ViewRecord
{
    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public static function getResource(): string
    {
        return config('filament-shield.shield_resource.role_resource') ?? RoleResource::class;
    }
}
