<?php

namespace BezhanSalleh\FilamentShield\Resources\UserResource\Pages;

use BezhanSalleh\FilamentShield\Resources\UserResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public Collection $permissions;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $except = ['name', 'email', 'password', 'select_all'];

        $this->permissions = collect($data)->filter(function ($permission, $key) use ($except) {
            return ! in_array($key, $except) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, $except);
    }

    protected function afterCreate(): void
    {
        $permissionModels = collect();
        $this->permissions->each(function ($permission) use ($permissionModels) {
            $permissionModels->push(Utils::getPermissionModel()::firstOrCreate(
                ['name' => $permission],
            ));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
