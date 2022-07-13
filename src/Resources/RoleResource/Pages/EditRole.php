<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Permission;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)->filter(function ($permission, $key) {
            return ! in_array($key, ['name','guard_name','select_all']) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, ['name','guard_name']);
    }

    protected function afterSave(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function($permission) use($permissionModels) {
            $permissionModels->push(Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => config('filament.auth.guard')]
            ));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
