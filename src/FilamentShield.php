<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Support\Concerns\EvaluatesClosures;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Spatie\Permission\PermissionRegistrar;

class FilamentShield
{
    use EvaluatesClosures;

    protected ?Closure $configurePermissionIdentifierUsing = null;

    public function configurePermissionIdentifierUsing(Closure $callback): static
    {
        $this->configurePermissionIdentifierUsing = $callback;

        return $this;
    }

    public function getPermissionIdentifier(string $resource): string
    {
        if ($this->configurePermissionIdentifierUsing) {

            $identifier = $this->evaluate(
                value: $this->configurePermissionIdentifierUsing,
                parameters: [
                    'resource' => $resource,
                ]
            );

            if (Str::contains($identifier, '_')) {
                throw new \InvalidArgumentException("Permission identifier `$identifier` for `$resource` cannot contain underscores.");
            }

            return $identifier;
        }

        return $this->getDefaultPermissionIdentifier($resource);
    }

    public function generateForResource(array $entity): void
    {
        $resourceByFQCN = $entity['fqcn'];
        $permissionPrefixes = Utils::getResourcePermissionPrefixes($resourceByFQCN);

        if (Utils::isResourceEntityEnabled()) {
            $permissions = collect();
            collect($permissionPrefixes)
                ->each(function ($prefix) use ($entity, $permissions) {
                    $permissions->push(Utils::getPermissionModel()::firstOrCreate(
                        ['name' => $prefix.'_'.$entity['resource']],
                        ['guard_name' => Utils::getFilamentAuthGuard()]
                    ));
                });

            static::giveSuperAdminPermission($permissions);
        }
    }

    public static function generateForPage(string $page): void
    {
        if (Utils::isPageEntityEnabled()) {
            $permission = Utils::getPermissionModel()::firstOrCreate(
                ['name' => $page],
                ['guard_name' => Utils::getFilamentAuthGuard()]
            )->name;

            static::giveSuperAdminPermission($permission);
        }
    }

    public static function generateForWidget(string $widget): void
    {
        if (Utils::isWidgetEntityEnabled()) {
            $permission = Utils::getPermissionModel()::firstOrCreate(
                ['name' => $widget],
                ['guard_name' => Utils::getFilamentAuthGuard()]
            )->name;

            static::giveSuperAdminPermission($permission);
        }
    }

    protected static function giveSuperAdminPermission(string|array|Collection $permissions): void
    {
        if (! Utils::isSuperAdminDefinedViaGate()) {
            $superAdmin = static::createRole();

            $superAdmin->givePermissionTo($permissions);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public static function createRole(bool $isSuperAdmin = true)
    {
        return Utils::getRoleModel()::firstOrCreate(
            ['name' => $isSuperAdmin ? Utils::getSuperAdminName() : Utils::getFilamentUserRoleName()],
            ['guard_name' => $isSuperAdmin ? Utils::getFilamentAuthGuard() : Utils::getFilamentAuthGuard()]
        );
    }

    /**
     * Transform filament resources to key value pair for shield
     *
     * @return array
     */
    public function getResources(): ?array
    {
        return collect(Filament::getResources())
            ->unique()
            ->filter(function ($resource) {
                if (Utils::isGeneralExcludeEnabled()) {
                    return ! in_array(
                        Str::of($resource)->afterLast('\\'),
                        Utils::getExcludedResouces()
                    );
                }

                return true;
            })
            ->reduce(function ($resources, $resource) {
                $name = $this->getPermissionIdentifier($resource);

                $resources["{$name}"] = [
                    'resource' => "{$name}",
                    'model' => Str::of($resource::getModel())->afterLast('\\'),
                    'fqcn' => $resource,
                ];

                return $resources;
            }, collect())
            ->sortKeys()
            ->toArray();
    }

    /**
     * Get the localized resource label
     */
    public static function getLocalizedResourceLabel(string $entity): string
    {
        $label = collect(Filament::getResources())->filter(function ($resource) use ($entity) {
            return $resource === $entity;
        })->first()::getModelLabel();

        return Str::of($label)->headline();
    }

    /**
     * Get the localized resource permission label
     */
    public static function getLocalizedResourcePermissionLabel(string $permission): string
    {
        return Lang::has("filament-shield::filament-shield.resource_permission_prefixes_labels.$permission", app()->getLocale())
            ? __("filament-shield::filament-shield.resource_permission_prefixes_labels.$permission")
            : Str::of($permission)->headline();
    }

    /**
     * Transform filament pages to key value pair for shield
     *
     * @return array
     */
    public static function getPages(): ?array
    {
        return collect(Filament::getPages())
            ->filter(function ($page) {
                if (Utils::isGeneralExcludeEnabled()) {
                    return ! in_array(Str::afterLast($page, '\\'), Utils::getExcludedPages());
                }

                return true;
            })
            ->reduce(function ($pages, $page) {
                $prepend = Str::of(Utils::getPagePermissionPrefix())->append('_');
                $name = Str::of(class_basename($page))
                    ->prepend($prepend);

                $pages["{$name}"] = "{$name}";

                return $pages;
            }, collect())
            ->toArray();
    }

    /**
     * Get localized page label
     */
    public static function getLocalizedPageLabel(string $page): string|bool
    {
        $object = static::transformClassString($page);

        if (Str::of($pageTitle = invade(new $object())->getTitle())->isNotEmpty()) {
            return $pageTitle;
        }

        return invade(new $object())->getNavigationLabel();
    }

    /**
     * Transform filament widgets to key value pair for shield
     *
     * @return array
     */
    public static function getWidgets(): ?array
    {
        return collect(Filament::getWidgets())
            ->filter(function ($widget) {
                if (Utils::isGeneralExcludeEnabled()) {
                    return ! in_array(Str::afterLast($widget, '\\'), Utils::getExcludedWidgets());
                }

                return true;
            })
            ->reduce(function ($widgets, $widget) {
                $prepend = Str::of(Utils::getWidgetPermissionPrefix())->append('_');
                $name = Str::of(class_basename($widget))
                    ->prepend($prepend);

                $widgets["{$name}"] = "{$name}";

                return $widgets;
            }, collect())
            ->toArray();
    }

    /**
     * Get localized widget label
     *
     * @param  string  $page
     * @return string|bool
     */
    public static function getLocalizedWidgetLabel(string $widget): string
    {
        $class = static::transformClassString($widget, false);
        $parent = get_parent_class($class);
        $grandpa = get_parent_class($parent);

        $heading = Str::of($widget)
            ->after(Utils::getPagePermissionPrefix().'_')
            ->headline();

        if ($grandpa === "Filament\Widgets\ChartWidget") {
            return (string) invade(new $class())->getHeading() ?? $heading;
        }

        return match ($parent) {
            "Filament\Widgets\TableWidget" => (string) invade(new $class())->getTableHeading(),
            "Filament\Widgets\StatsOverviewWidget" => (string) static::hasHeadingForShield($class)
                ? (new $class())->getHeadingForShield()
                : $heading,
            default => $heading
        };
    }

    protected static function transformClassString(string $string, bool $isPageClass = true): string
    {
        return (string) collect($isPageClass ? Filament::getPages() : Filament::getWidgets())
            ->first(fn ($item) => Str::endsWith(
                $item,
                Str::of($string)
                    ->after('_')
                    ->studly()
            ));
    }

    protected static function hasHeadingForShield(object|string $class): bool
    {
        return method_exists($class, 'getHeadingForShield');
    }

    protected function getDefaultPermissionIdentifier(string $resource): string
    {
        return Str::of($resource)
            ->afterLast('Resources\\')
            ->before('Resource')
            ->replace('\\', '')
            ->snake()
            ->replace('_', '::');
    }
}
