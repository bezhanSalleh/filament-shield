<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Resources\Roles\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Collection;
use Override;

class CreateRole extends CreateRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()], true))
            ->values()
            ->flatten()
            ->unique();

        return Utils::normalizeRoleFormData($data);
    }

    protected function afterCreate(): void
    {
        $permissionModels = Utils::buildPermissionModels($this->permissions, $this->data['guard_name']);

        $this->record->syncPermissions($permissionModels);
    }
}
