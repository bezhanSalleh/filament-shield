<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Support;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Traits\HasRoles;

class Utils
{
    protected static ?array $psr4Cache = null;

    public static function getConfig(): ShieldConfig
    {
        return ShieldConfig::init();
    }

    public static function getFilamentAuthGuard(): string
    {
        return Filament::getCurrentOrDefaultPanel()?->getAuthGuard() ?? '';
    }

    public static function getCurrentPanelId(): ?string
    {
        return Filament::getCurrentOrDefaultPanel()?->getId();
    }

    public static function isResourcePublished(Panel $panel): bool
    {
        return str(
            string: collect(value: $panel->getResources())
                ->values()
                ->join(',')
        )
            ->contains('\\RoleResource');
    }

    public static function getResourceSlug(): string
    {
        return (string) static::getConfig()->shield_resource->slug;
    }

    public static function getAuthProviderFQCN(): string
    {
        return (string) static::getConfig()->auth_provider_model;
    }

    public static function isAuthProviderConfigured(): bool
    {
        return in_array(HasRoles::class, class_uses_recursive(static::getAuthProviderFQCN()));
    }

    public static function isSuperAdminEnabled(): bool
    {
        return (bool) static::getConfig()->super_admin->enabled;
    }

    public static function getSuperAdminName(): string
    {
        return static::prefixRoleName((string) static::getConfig()->super_admin->name);
    }

    public static function isSuperAdminDefinedViaGate(): bool
    {
        return static::isSuperAdminEnabled() && static::getConfig()->super_admin->define_via_gate;
    }

    public static function getSuperAdminGateInterceptionStatus(): string
    {
        return (string) static::getConfig()->super_admin->intercept_gate;
    }

    public static function isPanelUserRoleEnabled(): bool
    {
        return (bool) static::getConfig()->panel_user->enabled;
    }

    public static function getPanelUserRoleName(): string
    {
        return static::prefixRoleName((string) static::getConfig()->panel_user->name);
    }

    public static function createPanelUserRole(): void
    {
        if (static::isPanelUserRoleEnabled()) {
            static::createRole(name: static::getPanelUserRoleName());
        }
    }

    public static function isResourceTabEnabled(): bool
    {
        return (bool) static::getConfig()->shield_resource->tabs->resources;
    }

    public static function isPageTabEnabled(): bool
    {
        return (bool) static::getConfig()->shield_resource->tabs->pages;
    }

    public static function isWidgetTabEnabled(): bool
    {
        return (bool) static::getConfig()->shield_resource->tabs->widgets;
    }

    public static function isCustomPermissionTabEnabled(): bool
    {
        return (bool) static::getConfig()->shield_resource->tabs->custom_permissions;
    }

    public static function getGeneratorOption(): string
    {
        return match (true) {
            static::getConfig()->permissions->generate && static::getConfig()->policies->generate => 'policies_and_permissions',
            static::getConfig()->permissions->generate => 'permissions',
            static::getConfig()->policies->generate => 'policies',
            default => 'none',
        };
    }

    public static function getPolicyPath(): string
    {
        $path = Str::of(static::getConfig()->policies->path ?? app_path('Policies'))
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->rtrim(DIRECTORY_SEPARATOR)
            ->toString();

        $panelSegment = static::getPolicyPanelSegment();
        if (filled($panelSegment)) {
            $path .= DIRECTORY_SEPARATOR . $panelSegment;
        }

        return $path;
    }

    public static function getPolicyPathRelativeToApp(): ?string
    {
        $policyPath = Str::of(static::getPolicyPath())
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->toString();

        $appPath = Str::of(app_path())
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->rtrim(DIRECTORY_SEPARATOR)
            ->toString();

        $prefix = $appPath . DIRECTORY_SEPARATOR;

        if (str_starts_with($policyPath, $prefix)) {
            return Str::of($policyPath)->after($prefix)->toString();
        }

        return null;
    }

    public static function getPolicyNamespaceSegment(): string
    {
        $relative = static::getPolicyPathRelativeToApp();
        if (filled($relative)) {
            return str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
        }

        return Str::of(static::resolveNamespaceFromPath(static::getPolicyPath()))
            ->afterLast('\\')
            ->toString();
    }

    public static function getRolePolicyPath(): ?string
    {
        $filesystem = new Filesystem;
        $path = static::getPolicyPath() . DIRECTORY_SEPARATOR . 'RolePolicy.php';

        return $filesystem->exists($path) ? Str::of(static::resolveNamespaceFromPath($path))->before('.php')->toString() : null;
    }

    public static function isPanelPolicyPathEnabled(): bool
    {
        return static::getConfig()->policiesPanelPathEnabled();
    }

    public static function isPolicyPathForced(): bool
    {
        return static::getConfig()->policiesForcePathEnabled();
    }

    public static function getPolicyPanelSegment(): ?string
    {
        if (! static::isPanelPolicyPathEnabled()) {
            return null;
        }

        $panel = Filament::getCurrentOrDefaultPanel();
        if (! $panel || $panel->isDefault()) {
            return null;
        }

        $panelId = $panel->getId();
        if (blank($panelId)) {
            return null;
        }

        return (string) Str::of($panelId)->studly();
    }

    public static function isRolePolicyRegistered(): bool
    {
        return filled(static::getRolePolicyPath()) && static::getConfig()->register_role_policy;
    }

    public static function showModelPath(string $resourceFQCN): string
    {
        return config('filament-shield.shield_resource.show_model_path', true)
            ? (new ($resourceFQCN::getModel())())::class
            : '';
    }

    public static function getResourceCluster(): ?string
    {
        return config('filament-shield.shield_resource.cluster');
    }

    public static function getRoleModel(): string
    {
        return app(PermissionRegistrar::class)
            ->getRoleClass();
    }

    public static function getPermissionModel(): string
    {
        return app(PermissionRegistrar::class)
            ->getPermissionClass();
    }

    public static function isTenancyEnabled(): bool
    {
        return (bool) config()->get('permission.teams', false);
    }

    public static function getTenantModelForeignKey(): string
    {
        return config()->get('permission.column_names.team_foreign_key', 'team_id');
    }

    public static function getTenantModel(): ?string
    {
        return static::getConfig()->tenant_model ?? null;
    }

    public static function extractRolePermissionsFromFormData(array $data): Collection
    {
        return collect($data)
            ->filter(fn (mixed $permission, string $key): bool => ! in_array($key, ['name', 'guard_name', 'select_all', static::getTenantModelForeignKey()]))
            ->values()
            ->flatten()
            ->unique();
    }

    public static function normalizeRoleFormData(array $data): array
    {
        $data['name'] = static::prefixRoleName((string) $data['name']);

        if (static::isTenancyEnabled() && Arr::has($data, static::getTenantModelForeignKey()) && filled($data[static::getTenantModelForeignKey()])) {
            return Arr::only($data, ['name', 'guard_name', static::getTenantModelForeignKey()]);
        }

        return Arr::only($data, ['name', 'guard_name']);
    }

    public static function buildPermissionModels(Collection $permissions, string $guardName): Collection
    {
        $permissionModels = collect();
        $permissions->each(function (string $permission) use ($permissionModels, $guardName): void {
            $permissionModels->push(static::getPermissionModel()::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ]));
        });

        return $permissionModels;
    }

    public static function createRole(?string $name = null, int | string | null $tenantId = null): Role
    {
        $guardName = static::getFilamentAuthGuard();
        $roleName = static::prefixRoleName($name ?? (string) static::getConfig()->super_admin->name);

        if (static::isTenancyEnabled()) {
            return static::getRoleModel()::firstOrCreate(
                [
                    'name' => $roleName,
                    static::getTenantModelForeignKey() => $tenantId,
                    'guard_name' => $guardName,
                ],
            );
        }

        return static::getRoleModel()::firstOrCreate(
            [
                'name' => $roleName,
                'guard_name' => $guardName,
            ],
        );
    }

    public static function createPermission(string $name): string
    {
        return static::getPermissionModel()::firstOrCreate(
            ['name' => $name, 'guard_name' => static::getFilamentAuthGuard()],
        )->name;
    }

    public static function isPanelPrefixEnabled(): bool
    {
        return static::getConfig()->permissionsPanelPrefixEnabled();
    }

    public static function isRolePanelPrefixEnabled(): bool
    {
        return static::getConfig()->rolesPanelPrefixEnabled();
    }

    public static function getRolePrefixSeparator(): string
    {
        return static::getConfig()->rolesPanelPrefixSeparator();
    }

    public static function getPanelRolePrefix(): ?string
    {
        if (! static::isRolePanelPrefixEnabled()) {
            return null;
        }

        $panel = Filament::getCurrentOrDefaultPanel();
        if (! $panel || $panel->isDefault()) {
            return null;
        }

        $panelId = $panel->getId();
        if (blank($panelId)) {
            return null;
        }

        return $panelId . static::getRolePrefixSeparator();
    }

    /**
     * @return array<int, string>
     */
    public static function getOtherPanelRolePrefixes(): array
    {
        if (! static::isRolePanelPrefixEnabled()) {
            return [];
        }

        $separator = static::getRolePrefixSeparator();
        $panels = Filament::getPanels();

        if (! is_array($panels) || $panels === []) {
            return [];
        }

        return collect($panels)
            ->filter(fn (Panel $panel): bool => ! $panel->isDefault() && filled($panel->getId()))
            ->map(fn (Panel $panel): string => $panel->getId() . $separator)
            ->unique()
            ->values()
            ->all();
    }

    public static function prefixRoleName(string $name): string
    {
        $prefix = static::getPanelRolePrefix();
        if (blank($prefix)) {
            return $name;
        }

        if (str_starts_with($name, $prefix)) {
            return $name;
        }

        return $prefix . $name;
    }

    public static function stripPanelRolePrefix(string $name): string
    {
        $prefix = static::getPanelRolePrefix();
        if (blank($prefix)) {
            return $name;
        }

        return str_starts_with($name, $prefix)
            ? substr($name, strlen($prefix))
            : $name;
    }

    public static function getPanelPermissionPrefix(): ?string
    {
        if (! static::isPanelPrefixEnabled()) {
            return null;
        }

        $panelId = static::getCurrentPanelId();
        if (blank($panelId)) {
            return null;
        }

        return $panelId . static::getPanelPrefixSeparator();
    }

    public static function getPanelPrefixSeparator(): string
    {
        return static::getConfig()->permissionsPanelPrefixSeparator();
    }

    public static function prefixPermissionWithPanel(string $permission): string
    {
        if (! static::isPanelPrefixEnabled()) {
            return $permission;
        }

        $panelId = static::getCurrentPanelId();
        if (blank($panelId)) {
            return $permission;
        }

        $separator = static::getPanelPrefixSeparator();
        $prefix = $panelId . $separator;

        if (str_starts_with($permission, $prefix)) {
            return $permission;
        }

        return $prefix . $permission;
    }

    public static function giveSuperAdminPermission(string | array | Collection $permissions): void
    {
        if (! static::isSuperAdminDefinedViaGate() && static::isSuperAdminEnabled()) {

            if (static::isTenancyEnabled() && $tenantModel = static::getTenantModel()) {

                $tenants = app($tenantModel)->all();

                foreach ($tenants as $tenant) {
                    $superAdmin = static::createRole(tenantId: $tenant->getKey());
                    $superAdmin->givePermissionTo($permissions);
                }

            } else {
                $superAdmin = static::createRole();
                $superAdmin->givePermissionTo($permissions);
            }

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public static function generateForResource(string $resourceKey): void
    {
        $permissions = collect(FilamentShield::getResourcePermissions($resourceKey))
            ->map(static::createPermission(...))
            ->toArray();

        static::giveSuperAdminPermission($permissions);
    }

    public static function generateForPageOrWidget(string $name): void
    {
        static::giveSuperAdminPermission(static::createPermission($name));
    }

    public static function generateForExtraPermissions(): void
    {
        $customPermissions = collect(FilamentShield::getCustomPermissions())->keys();

        if ($customPermissions->isNotEmpty()) {
            $permissions = $customPermissions
                ->map(static::createPermission(...))
                ->toArray();

            static::giveSuperAdminPermission($permissions);
        }
    }

    public static function resolveNamespaceFromPath(string $configuredPath): string
    {
        // Cache PSR-4 mappings to avoid repeated file I/O
        if (static::$psr4Cache === null) {
            $composer = json_decode(file_get_contents(base_path('composer.json')), true);
            static::$psr4Cache = $composer['autoload']['psr-4'] ?? [];
        }

        // Normalize path separators once
        $configuredPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $configuredPath);

        // Convert relative path to absolute
        if (! static::isAbsolutePath($configuredPath)) {
            $configuredPath = base_path($configuredPath);
        }

        // Normalize and prepare for comparison
        $checkPath = rtrim($configuredPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $checkPathLower = strtolower($checkPath);

        foreach (static::$psr4Cache as $namespace => $base) {
            $basePath = rtrim(base_path(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $base)), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $basePathLower = strtolower($basePath);

            // Fast path: exact match
            if ($checkPathLower === $basePathLower) {
                return rtrim((string) $namespace, '\\');
            }

            // Check if configured path is within this PSR-4 base
            if (str_starts_with($checkPathLower, $basePathLower)) {
                $relative = substr($checkPath, strlen($basePath));
                $relative = rtrim($relative, DIRECTORY_SEPARATOR);

                $ns = rtrim((string) $namespace, '\\');
                if ($relative !== '') {
                    $ns .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
                }

                return $ns;
            }
        }

        throw new RuntimeException('Configured path does not match any PSR-4 mapping: ' . $configuredPath);
    }

    /**
     * Convert a permission key to a localization key.
     *
     * Removes the configured separator and converts to snake_case.
     */
    public static function toLocalizationKey(string $key): string
    {
        $separator = static::getConfig()->permissions->separator;

        return Str::of($key)
            ->replace($separator, '_')
            ->snake()
            ->replace('__', '_')
            ->toString();
    }

    protected static function isAbsolutePath(string $path): bool
    {
        // windows os
        if (preg_match('/^[a-zA-Z]:[\\\\\\/]/', $path)) {
            return true;
        }

        return str_starts_with($path, DIRECTORY_SEPARATOR);
    }
}
