<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Support;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

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
        return in_array(\Spatie\Permission\Traits\HasRoles::class, class_uses_recursive(static::getAuthProviderFQCN()));
    }

    public static function isSuperAdminEnabled(): bool
    {
        return (bool) static::getConfig()->super_admin->enabled;
    }

    public static function getSuperAdminName(): string
    {
        return (string) static::getConfig()->super_admin->name;
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
        return (string) static::getConfig()->panel_user->name;
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
        return Str::of(static::getConfig()->policies->path ?? app_path('Policies'))
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->toString();
    }

    public static function getRolePolicyPath(): ?string
    {
        $filesystem = new Filesystem;
        $path = static::getPolicyPath() . DIRECTORY_SEPARATOR . 'RolePolicy.php';

        return $filesystem->exists($path) ? Str::of(static::resolveNamespaceFromPath($path))->before('.php')->toString() : null;
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
        return config('filament-shield.shield_resource.cluster', null);
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

    public static function createRole(?string $name = null, int | string | null $tenantId = null): Role
    {
        if (static::isTenancyEnabled()) {
            return static::getRoleModel()::firstOrCreate(
                [
                    'name' => $name ?? static::getConfig()->super_admin->name,
                    static::getTenantModelForeignKey() => $tenantId,
                ],
                ['guard_name' => static::getFilamentAuthGuard()]
            );
        }

        return static::getRoleModel()::firstOrCreate(
            ['name' => $name ?? static::getSuperAdminName()],
            ['guard_name' => static::getFilamentAuthGuard()]
        );
    }

    public static function createPermission(string $name): string
    {
        return static::getPermissionModel()::firstOrCreate(
            ['name' => $name],
            ['guard_name' => static::getFilamentAuthGuard()]
        )->name;
    }

    public static function giveSuperAdminPermission(string | array | Collection $permissions): void
    {
        if (! static::isSuperAdminDefinedViaGate() && static::isSuperAdminEnabled()) {
            $superAdmin = static::createRole();

            $superAdmin->givePermissionTo($permissions);

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
                return rtrim($namespace, '\\');
            }

            // Check if configured path is within this PSR-4 base
            if (str_starts_with($checkPathLower, $basePathLower)) {
                $relative = substr($checkPath, strlen($basePath));
                $relative = rtrim($relative, DIRECTORY_SEPARATOR);

                $ns = rtrim($namespace, '\\');
                if ($relative !== '') {
                    $ns .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
                }

                return $ns;
            }
        }

        throw new \RuntimeException("Configured path does not match any PSR-4 mapping: {$configuredPath}");
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
