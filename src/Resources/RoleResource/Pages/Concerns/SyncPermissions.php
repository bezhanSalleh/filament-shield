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

    private function getPermissions($data, string $type): Collection
    {
        /**
         * Get the permissions from the provided data based on the given type.
         *
         * @param  array  $data The data form.
         * @param  string  $type The type of permissions to retrieve (e.g., resource, page, widget).
         * @return Collection The collection of permissions [type => [permission, ...], ...].
         */
        return collect($data)
            ->reject(fn ($resource, $key) => ! Str::contains($key, $type.'_'))
            ->mapWithKeys(fn ($resource, $key) => [
                Str::replaceFirst($type.'_', '', $key) => $resource,
            ]);
    }

    private function getTypeValues($type): Collection
    {
        /**
         * Get the values for the given type.
         *
         * @param  string  $type The type of values to retrieve (e.g., resource, page, widget).
         * @return Collection The collection of values.
         */
        $allowedTypes = ['resource', 'page', 'widget'];

        if (! in_array($type, $allowedTypes)) {
            throw new \Exception('Invalid type provided.');
        }

        return $this->{$type.'s'};
    }

    private function syncPermissions($data, $type): void
    {
        /**
         * Sync the permissions based on the provided data and type.
         *
         * @param  mixed  $data The data containing the permissions.
         * @param  string  $type The type of permissions to sync (e.g., resource, page, widget).
         * @return void
         */
        $permissionModels = $this->{$type.'s'}
            ->flatMap(function ($permissions, $resource) {
                return collect($permissions)->map(function ($permission) use ($resource) {
                    return Utils::getPermissionModel()::firstOrCreate([
                        'name' => $permission.'_'.$resource,
                        'guard_name' => $this->data['guard_name'],
                    ]);
                });
            })
            ->all();

        $this->record->syncPermissions($permissionModels);
    }
}
