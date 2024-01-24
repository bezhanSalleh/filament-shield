<?php

namespace BezhanSalleh\FilamentShield\Support;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class Utils
{
    public static function getFilamentAuthGuard(): string
    {
        return Filament::getCurrentPanel()?->getAuthGuard() ?? '';
    }

    public static function isResourcePublished(): bool
    {
        $roleResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield\\RoleResource.php')->replace('\\', '/'));

        $filesystem = new Filesystem();

        return (bool) $filesystem->exists($roleResourcePath);
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

    public static function getExcludedWidgets(): array
    {
        return config('filament-shield.exclude.widgets');
    }

    public static function isRolePolicyRegistered(): bool
    {
        return (bool) config('filament-shield.register_role_policy', true);
    }

    public static function doesResourceHaveCustomPermissions(string $resourceClass): bool
    {
        return in_array(HasShieldPermissions::class, class_implements($resourceClass));
    }

    public static function showModelPath(string $resourceFQCN): string
    {
        $reflectionClass = new \ReflectionClass($resourceFQCN);
        try {
            $reflectionClass->getProperty('model');
            if (!$reflectionClass->getStaticPropertyValue('model')) {
                throw new \ErrorException();
            }
        } catch (\Exception $e) {
            try {
                $reflectionClass->getMethod('getModel');
                $resourceFQCN = $resourceFQCN::getModel();
            } catch (\Exception $e) {
                try {
                    $reflectionClass->getMethod('setModel');
                    $resourceFQCN = $resourceFQCN::setModel();
                } catch (\Exception $e) {
                }
            }
        }
        return config('filament-shield.shield_resource.show_model_path', true)
            ? get_class(new ($resourceFQCN::getModel())())
            : '';
    }

    public static function getResourcePermissionPrefixes(string $resourceFQCN): array
    {
        return static::doesResourceHaveCustomPermissions($resourceFQCN)
            ? $resourceFQCN::getPermissionPrefixes()
            : static::getGeneralResourcePermissionPrefixes();
    }

    public static function getRoleModel(): string
    {
        return config('permission.models.role', 'Spatie\\Permission\\Models\\Role');
    }

    public static function getPermissionModel(): string
    {
        return config('permission.models.permission', 'Spatie\\Permission\\Models\\Permission');
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
}
