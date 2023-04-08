<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use Filament\Pages\Actions;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Filament\Resources\Pages\EditRecord;
use BezhanSalleh\FilamentShield\ShieldManager;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Resources\RoleResource;

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
            return ! in_array($key, ['name', 'guard_name', 'title', 'select_all']) && Str::contains($key, '_');
        })->keys();

        return Arr::only($data, Utils::isShieldUsingBouncerDriver() ? ['name','title'] : ['name', 'guard_name']);
    }

    protected function afterSave(): void
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
        // ray($permissions)->die();
        ShieldManager::giveRolePermissions($this->record, $permissions);
    }
}
