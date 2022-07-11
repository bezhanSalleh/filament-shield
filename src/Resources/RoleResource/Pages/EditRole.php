<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    public Collection $permissions;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

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
        $this->permissions->each(function ($permission) use ($permissionModels) {
            $permissionModels->push(Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => config('filament.auth.guard')]
            ));
        });

        $this->record->syncPermissions($permissionModels);
    }
}
