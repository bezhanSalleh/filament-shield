<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\Concerns\SyncPermissions;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;

class EditRole extends EditRecord
{
    use SyncPermissions;

    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->resources = $this->getPermissions($data, 'resource');
        // $this->pages = $this->getPermissions($data, 'page');
        // $this->widgets = $this->getPermissions($data, 'widget');

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {
        $this->syncPermissions($this->resources, 'resource');
        // $this->syncPermissions($this->pages, 'page');
        // $this->syncPermissions($this->widgets, 'widget');
    }
}
