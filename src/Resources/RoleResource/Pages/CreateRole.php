<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\Concerns\SyncPermissions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;

class CreateRole extends CreateRecord
{
    use SyncPermissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->resources = $this->getPermissions($data, 'resource');
        // $this->pages = $this->getPermissions($data, 'page');
        // $this->widgets = $this->getPermissions($data, 'widget');

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterCreate(): void
    {
        $this->syncPermissions($this->resources, 'resource');
        // $this->syncPermissions($this->pages, 'page');
        // $this->syncPermissions($this->widgets, 'widget');
    }
}
