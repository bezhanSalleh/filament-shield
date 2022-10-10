<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)->filter(function ($permission, $key) {
            return ! in_array($key, ['name', 'guard_name', 'select_all']) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterCreate(): void
    {
	$permissionModel = Utils::getPermissionModel();
        $permissionModels = collect();
        $this->permissions->each(function ($permission) use ($permissionModel, $permissionModels) {
            $permissionModels->push((new $permissionModel())::firstOrCreate(
                /** @phpstan-ignore-next-line */
                ['name' => $permission],
                ['guard_name' => $this->data['guard_name']]
            ));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
