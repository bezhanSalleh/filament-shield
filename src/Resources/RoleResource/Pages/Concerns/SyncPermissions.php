<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait SyncPermissions
{
    public Collection $resources;

    public Collection $pages;

    public Collection $widgets;

    private function mutateData($data): void
    {
        $this->pages = collect($data['pages'] ?? []);

        $this->widgets = collect($data['widgets'] ?? []);

        $this->resources = collect($data)
            ->reject(fn ($resource, $key) => ! Str::contains($key, 'resource_'))
            ->values()
            ->flatten();
    }

    private function syncPermissions(): void
    {
        $permissions = $this->resources->merge($this->pages)->merge($this->widgets);

        $permissionModels = $permissions
            ->map(function ($permission) {
                return Utils::getPermissionModel()::firstOrCreate([
                    'name' => $permission,
                    'guard_name' => $this->data['guard_name'],
                ]);
            })
            ->all();

        $this->record->syncPermissions($permissionModels);
    }
}
