<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $resources;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->resources = collect($data)
            ->reject(fn ($resource, $key) => in_array($key, ['name', 'guard_name', 'select_all']) || ! Str::contains($key, 'resource_')
            )
            ->mapWithKeys(fn ($resource, $key) => [
                Str::replaceFirst('resource_', '', $key) => $resource,
            ]);

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {

        $permissionModels = $this->resources
            ->flatMap(fn ($permissions, $resource) => collect($permissions)->map(fn ($permission) => Utils::getPermissionModel()::firstOrCreate([
                'name' => $permission.'_'.$resource,
                'guard_name' => $this->data['guard_name'],
            ]))
            )
            ->all();

        $this->record->syncPermissions($permissionModels);
    }
}
