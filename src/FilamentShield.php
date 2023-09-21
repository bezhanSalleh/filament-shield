<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Facades\Filament;
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
                namedInjections: [
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
                        ['name' => $prefix . '_' . $entity['resource']],
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

    protected static function giveSuperAdminPermission(string | array | Collection $permissions): void
    {
        if (! Utils::isSuperAdminDefinedViaGate()) {
            $superAdmin = static::createRole();

            $superAdmin->givePermissionTo($permissions);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public static function createRole()
    {
        return Utils::getRoleModel()::firstOrCreate(
            ['name' => Utils::getSuperAdminName()],
            ['guard_name' => Utils::getFilamentAuthGuard()]
        );
    }

    /**
     * Transform filament resources to key value pair for shield
     */
    public function getResources(): ?array
    {
        $resources = Filament::getResources();
        if (Utils::discoverAllResources()) {
            $resources = [];
            foreach (Filament::getPanels() as $panel) {
                $resources = array_merge($resources, $panel->getResources());
            }
            $resources = array_unique($resources);
        }

        return collect($resources)
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
        $resources = Filament::getResources();
        if (Utils::discoverAllResources()) {
            $resources = [];
            foreach (Filament::getPanels() as $panel) {
                $resources = array_merge($resources, $panel->getResources());
            }
            $resources = array_unique($resources);
        }
        $label = collect($resources)->filter(function ($resource) use ($entity) {
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
     */
    public static function getPages(): ?array
    {
        $pages = Filament::getPages();
        if (Utils::discoverAllPages()) {
            $pages = [];
            foreach (Filament::getPanels() as $panel) {
                $pages = array_merge($pages, $panel->getPages());
            }
            $pages = array_unique($pages);
        }

        return collect($pages)
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
    public static function getLocalizedPageLabel(string $page): string
    {
        $object = static::transformClassString($page);

        $pageObject = invade(new $object());

        return $pageObject->getTitle()
                ?? $pageObject->getHeading()
                ?? $pageObject->getNavigationLabel()
                ?? '';
    }

    /**
     * Transform filament widgets to key value pair for shield
     */
    public static function getWidgets(): ?array
    {
        $widgets = Filament::getWidgets();
        if (Utils::discoverAllWidgets()) {
            $widgets = [];
            foreach (Filament::getPanels() as $panel) {
                $widgets = array_merge($widgets, $panel->getWidgets());
            }
            $widgets = array_unique($widgets);
        }

        return collect($widgets)
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
            ->after(Utils::getPagePermissionPrefix() . '_')
            ->headline();

        if ($grandpa === "Filament\Widgets\ChartWidget") {
            return (string) (invade(new $class())->getHeading() ?? $heading);
        }

        return match ($parent) {
            "Filament\Widgets\TableWidget" => (string) invade(new $class())->makeTable()->getHeading(),
            "Filament\Widgets\StatsOverviewWidget" => (string) static::hasHeadingForShield($class)
                ? (new $class())->getHeadingForShield()
                : $heading,
            default => $heading
        };
    }

    protected static function transformClassString(string $string, bool $isPageClass = true): string
    {
        $pages = Filament::getPages();
        if (Utils::discoverAllPages()) {
            $pages = [];
            foreach (Filament::getPanels() as $panel) {
                $pages = array_merge($pages, $panel->getPages());
            }
            $pages = array_unique($pages);
        }

        $widgets = Filament::getWidgets();
        if (Utils::discoverAllWidgets()) {
            $widgets = [];
            foreach (Filament::getPanels() as $panel) {
                $widgets = array_merge($widgets, $panel->getWidgets());
            }
            $widgets = array_unique($widgets);
        }

        $prefix = Str::of($isPageClass ? Utils::getPagePermissionPrefix() : Utils::getWidgetPermissionPrefix())->append('_');

        return (string) collect($isPageClass ? $pages : $widgets)
            ->first(fn ($item) => class_basename($item) == Str::of($string)->after($prefix)->studly());
    }

    protected static function hasHeadingForShield(object | string $class): bool
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
