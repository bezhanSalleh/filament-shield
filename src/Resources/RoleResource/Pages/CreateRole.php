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
        $this->mutateData($data);

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterCreate(): void
    {
        $this->syncPermissions();
    }
}
