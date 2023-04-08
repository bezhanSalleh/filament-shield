<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Filament\Resources\Pages\CreateRecord;
use BezhanSalleh\FilamentShield\ShieldManager;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)->filter(function ($permission, $key) {
            return ! in_array($key, ['name', 'guard_name', 'title','select_all']) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, Utils::isShieldUsingBouncerDriver() ? ['name','title'] : ['name', 'guard_name']);
    }

    protected function afterCreate(): void
    {
        $permissions = collect();
        $this->permissions->each(function ($permission) use ($permissions) {
            $permissions->push(
                ShieldManager::firstOrCreate('permission', [
                    'name' => $permission,
                    'guard_name' => Utils::getFilamentAuthGuard(),
                ])
                ->name
            );
        });

        ShieldManager::giveRolePermissions($this->record, $permissions);
    }
}
