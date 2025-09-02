<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

trait HasResourceHelpers
{
    public function getResourcePermissions(string $key): ?array
    {
        return array_values($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePolicyActions(string $key): ?array
    {
        return array_keys($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePermissionsWithLabels(string $key): ?array
    {
        return collect(
            data_get(
                target: $this->getResources(),
                key: "$key.permissions"
            )
        )
            ->mapWithKeys(fn (array $permission): array => [$permission['key'] => $permission['label']])
            ->toArray();
    }

    public function getResourcePolicyActionsWithPermissions(string $key): ?array
    {
        return collect(data_get(
            target: $this->getResources(),
            key: "$key.permissions"
        ))
            ->mapWithKeys(fn (array $permission, string $action): array => [$action => $permission['key']])
            ->toArray();
    }

    public function getAllResourcePermissionsWithLabels(): array
    {
        return once(
            fn (): array => collect($this->getResources())
                ->flatMap(
                    fn (array $resource): array => $this->getResourcePermissionsWithLabels(
                        $resource['resourceFqcn']
                    )
                )
                ->toArray()
        );
    }
}
