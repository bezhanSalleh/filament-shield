<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Resources\Roles\Pages;

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Override;

class EditRole extends EditRecord
{
    public Collection $permissions;

    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all', Utils::getTenantModelForeignKey()], true))
            ->values()
            ->flatten()
            ->unique();

        if (Utils::isTenancyEnabled() && Arr::has($data, Utils::getTenantModelForeignKey()) && filled($data[Utils::getTenantModelForeignKey()])) {
            return Arr::only($data, ['name', 'guard_name', Utils::getTenantModelForeignKey()]);
        }

        return Utils::normalizeRoleFormData($data);
    }

    protected function afterSave(): void
    {
        $permissionModels = Utils::buildPermissionModels($this->permissions, $this->data['guard_name']);

        $record = $this->getRoleRecord();
        $panelPrefix = Utils::getPanelPermissionPrefix();
        if (filled($panelPrefix)) {
            $otherPanelPermissions = $record->permissions()
                ->pluck('name')
                ->filter(fn (string $name): bool => ! Str::startsWith($name, $panelPrefix))
                ->values();

            $permissionNames = $permissionModels->pluck('name')
                ->merge($otherPanelPermissions)
                ->unique()
                ->values();

            $permissionModels = Utils::getPermissionModel()::whereIn('name', $permissionNames)
                ->where('guard_name', $this->data['guard_name'])
                ->get();
        }

        $record->syncPermissions($permissionModels);
    }

    /**
     * @return Model&RoleContract
     */
    protected function getRoleRecord(): Model
    {
        /** @var Model&RoleContract $record */
        $record = $this->record;

        return $record;
    }
}
