<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('setting')
                ->label(__('filament-shield::filament-shield.page.name'))
                ->url(static::$resource::getUrl('settings'))
                ->icon(__('filament-shield::filament-shield.page.icon'))
                ->color('primary')
                ->outlined(),
        ];
    }
}
