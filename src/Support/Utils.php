<?php

namespace BezhanSalleh\FilamentShield\Support;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class Utils
{
    public static function getFilamentAuthGuard(): string
    {
        return Filament::getCurrentPanel()?->getAuthGuard() ?? '';
    }

    public static function isResourcePublished(Panel $panel): bool
    {
        return str(
            string: collect(value: $panel->getResources())
                ->values()
                ->join(',')
        )
            ->contains('RoleResource');
    }

    public static function getResourceSlug(): string
    {
        return (string) config('filament-shield.shield_resource.slug');
    }

    public static function isResourceNavigationRegistered(): bool
    {
        return config('filament-shield.shield_resource.should_register_navigation', true);
    }

    public static function getResourceNavigationSort(): ?int
    {
        return config('filament-shield.shield_resource.navigation_sort');
    }

    public static function isResourceNavigationBadgeEnabled(): bool
    {
        return config('filament-shield.shield_resource.navigation_badge', true);
    }

    public static function isScopedToTenant(): bool
    {
        return config('filament-shield.shield_resource.is_scoped_to_tenant', true);
    }

    public static function isResourceNavigationGroupEnabled(): bool
    {
        return config('filament-shield.shield_resource.navigation_group', true);
    }

    public static function isResourceGloballySearchable(): bool
    {
        return config('filament-shield.shield_resource.is_globally_searchable', false);
    }

    public static function getAuthProviderFQCN()
    {
        return config('filament-shield.auth_provider_model.fqcn');
    }

    public static function isAuthProviderConfigured(): bool
    {
        return in_array("Spatie\Permission\Traits\HasRoles", class_uses_recursive(static::getAuthProviderFQCN()));
    }

    public static function isSuperAdminEnabled(): bool
    {
        return (bool) config('filament-shield.super_admin.enabled', true);
    }

    public static function getSuperAdminName(): string
    {
        return (string) config('filament-shield.super_admin.name');
    }

    public static function isSuperAdminDefinedViaGate(): bool
    {
        return (bool) static::isSuperAdminEnabled() && config('filament-shield.super_admin.define_via_gate', false);
    }

    public static function getSuperAdminGateInterceptionStatus(): string
    {
        return (string) config('filament-shield.super_admin.intercept_gate');
    }

    public static function isPanelUserRoleEnabled(): bool
    {
        return (bool) config('filament-shield.panel_user.enabled', false);
    }

    public static function getPanelUserRoleName(): string
    {
        return (string) config('filament-shield.panel_user.name', 'panel_user');
    }

    public static function createPanelUserRole(): void
    {
        if (static::isPanelUserRoleEnabled()) {
            FilamentShield::createRole(name: Utils::getPanelUserRoleName());
        }
    }

    public static function getGeneralResourcePermissionPrefixes(): array
    {
        return config('filament-shield.permission_prefixes.resource');
    }

    public static function getPagePermissionPrefix(): string
    {
        return (string) config('filament-shield.permission_prefixes.page');
    }

    public static function getWidgetPermissionPrefix(): string
    {
        return (string) config('filament-shield.permission_prefixes.widget');
    }

    public static function isResourceEntityEnabled(): bool
    {
        return (bool) config('filament-shield.entities.resources', true);
    }

    public static function isPageEntityEnabled(): bool
    {
        return (bool) config('filament-shield.entities.pages', true);
    }

    /**
     * Widget Entity Status
     */
    public static function isWidgetEntityEnabled(): bool
    {
        return (bool) config('filament-shield.entities.widgets', true);
    }

    public static function isCustomPermissionEntityEnabled(): bool
    {
        return (bool) config('filament-shield.entities.custom_permissions', false);
    }

    public static function getGeneratorOption(): string
    {
        return (string) config('filament-shield.generator.option', 'policies_and_permissions');
    }

    public static function getGeneratorNamespace(): string
    {
        return (string) config('filament-shield.generator.namespace', 'Policies');
    }

    public static function isGeneralExcludeEnabled(): bool
    {
        return (bool) config('filament-shield.exclude.enabled', true);
    }

    public static function enableGeneralExclude(): void
    {
        config(['filament-shield.exclude.enabled' => true]);
    }

    public static function disableGeneralExclude(): void
    {
        config(['filament-shield.exclude.enabled' => false]);
    }

    public static function getExcludedResouces(): array
    {
        return config('filament-shield.exclude.resources');
    }

    public static function getExcludedPages(): array
    {
        return config('filament-shield.exclude.pages');
    }

    public static function getPolicyNamespace(): string
    {
        return (string) config('filament-shield.generator.policy_namespace', 'Policies');
    }

    public static function getExcludedWidgets(): array
    {
        return config('filament-shield.exclude.widgets');
    }

    public static function isRolePolicyRegistered(): bool
    {
        return static::isRolePolicyGenerated() && config('filament-shield.register_role_policy.enabled', false);
    }

    public static function doesResourceHaveCustomPermissions(string $resourceClass): bool
    {
        return in_array(HasShieldPermissions::class, class_implements($resourceClass));
    }

    public static function showModelPath(string $resourceFQCN): string
    {
        return config('filament-shield.shield_resource.show_model_path', true)
            ? get_class(new ($resourceFQCN::getModel())())
            : '';
    }

    public static function getResourceCluster(): ?string
    {
        return config('filament-shield.shield_resource.cluster', null);
    }

    public static function getResourcePermissionPrefixes(string $resourceFQCN): array
    {
        return static::doesResourceHaveCustomPermissions($resourceFQCN)
            ? $resourceFQCN::getPermissionPrefixes()
            : static::getGeneralResourcePermissionPrefixes();
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

    public static function discoverAllResources(): bool
    {
        return config('filament-shield.discovery.discover_all_resources', false);
    }

    public static function discoverAllWidgets(): bool
    {
        return config('filament-shield.discovery.discover_all_widgets', false);
    }

    public static function discoverAllPages(): bool
    {
        return config('filament-shield.discovery.discover_all_pages', false);
    }

    public static function getPolicyPath(): string
    {
        return Str::of(config('filament-shield.generator.policy_directory', 'Policies'))
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->toString();
    }

    protected static function isRolePolicyGenerated(): bool
    {
        $filesystem = new Filesystem;

        return (bool) $filesystem->exists(app_path(static::getPolicyPath() . DIRECTORY_SEPARATOR . 'RolePolicy.php'));
    }

    public static function isTenancyEnabled(): bool
    {
        return (bool) config()->get('permission.teams', false);
    }

    public static function getTenantModel(): ?string
    {
        return config()->get('filament-shield.tenant_model', null);
    }

    public static function getTenantModelForeignKey(): string
    {
        return config()->get('permission.column_names.team_foreign_key', 'team_id');
    }
}
