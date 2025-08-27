<?php

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
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

            $identifier = (string) $this->evaluate(
                value: $this->configurePermissionIdentifierUsing,
                namedInjections: [
                    'resource' => $resource,
                ]
            );

            if (Str::contains($identifier, '_')) {
                throw new InvalidArgumentException("Permission identifier `$identifier` for `$resource` cannot contain underscores.");
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
                ->each(function (string $prefix) use ($entity, $permissions) {
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

    public static function generateCustomPermissions(): void
    {
        $customPermissions = collect(static::getCustomPermissions())->keys();

        if (Utils::isCustomPermissionEntityEnabled()) {

            if ($customPermissions->isNotEmpty()) {
                $permissions = $customPermissions
                    ->map(function (string $permission): string {
                        return Utils::getPermissionModel()::firstOrCreate(
                            ['name' => $permission],
                            ['guard_name' => Utils::getFilamentAuthGuard()]
                        )->name;
                    })
                    ->toArray();
                static::giveSuperAdminPermission($permissions);
            }
        }
    }

    protected static function giveSuperAdminPermission(string | array | Collection $permissions): void
    {
        if (! Utils::isSuperAdminDefinedViaGate() && Utils::isSuperAdminEnabled()) {
            $superAdmin = static::createRole();

            $superAdmin->givePermissionTo($permissions);

            app(PermissionRegistrar::class)->forgetCachedPermissions();
        }
    }

    public static function createRole(?string $name = null, int | string | null $tenantId = null): Role
    {
        if (Utils::isTenancyEnabled()) {
            return Utils::getRoleModel()::firstOrCreate(
                [
                    'name' => $name ?? Utils::getSuperAdminName(),
                    Utils::getTenantModelForeignKey() => $tenantId,
                ],
                ['guard_name' => Utils::getFilamentAuthGuard()]
            );
        }

        return Utils::getRoleModel()::firstOrCreate(
            ['name' => $name ?? Utils::getSuperAdminName()],
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
            ->reject(function (string $resource): bool {
                if (Utils::isGeneralExcludeEnabled()) {
                    return in_array(
                        Str::of($resource)->afterLast('\\'),
                        Utils::getExcludedResouces()
                    );
                }

                return false;
            })
            ->mapWithKeys(function (string $resource): array {
                $name = $this->getPermissionIdentifier($resource);

                return [
                    $name => [
                        'resource' => "{$name}",
                        'model' => str($resource::getModel())->afterLast('\\')->toString(),
                        'fqcn' => $resource,
                    ],
                ];
            })
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
        $label = collect($resources)->filter(function (string $resource) use ($entity): bool {
            return $resource === $entity;
        })->first()::getModelLabel();

        return str($label)->headline()->toString();
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

        $clusters = collect($pages)
            ->map(fn (string $page): ?string => $page::getCluster())
            ->reject(fn (mixed $cluster): bool => is_null($cluster))
            ->unique()
            ->values()
            ->toArray();

        return collect($pages)
            ->reject(function (string $page) use ($clusters): bool {
                if (in_array($page, $clusters)) {
                    return true;
                }

                if (Utils::isGeneralExcludeEnabled()) {
                    return in_array(Str::afterLast($page, '\\'), Utils::getExcludedPages());
                }

                return false;
            })
            ->mapWithKeys(function (string $page): array {
                $permission = Str::of(class_basename($page))
                    ->prepend(
                        Str::of(Utils::getPagePermissionPrefix())
                            ->append('_')
                            ->toString()
                    )
                    ->toString();

                return [
                    $permission => [
                        'class' => $page,
                        'permission' => $permission,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Get localized page label
     */
    public static function getLocalizedPageLabel(string $page): string
    {
        $pageInstance = app()->make($page);

        return $pageInstance->getTitle()
                ?? $pageInstance->getHeading()
                ?? $pageInstance->getNavigationLabel()
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
            ->reject(function (string | WidgetConfiguration $widget): bool {
                if (Utils::isGeneralExcludeEnabled()) {
                    return in_array(
                        needle: str(
                            static::getWidgetInstanceFromWidgetConfiguration($widget)
                        )
                            ->afterLast('\\')
                            ->toString(),
                        haystack: Utils::getExcludedWidgets()
                    );
                }

                return false;
            })
            ->mapWithKeys(function (string | WidgetConfiguration $widget): array {
                $permission = Str::of(class_basename(static::getWidgetInstanceFromWidgetConfiguration($widget)))
                    ->prepend(
                        Str::of(Utils::getWidgetPermissionPrefix())
                            ->append('_')
                            ->toString()
                    )
                    ->toString();

                return [
                    $permission => [
                        'class' => static::getWidgetInstanceFromWidgetConfiguration($widget),
                        'permission' => $permission,
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Get localized widget label
     */
    public static function getLocalizedWidgetLabel(string $widget): string
    {
        $widgetInstance = app()->make($widget);

        return match (true) {
            $widgetInstance instanceof TableWidget => (string) invade($widgetInstance)->makeTable()->getHeading(), // @phpstan-ignore-line
            self::hasValidHeading($widgetInstance) => (string) invade($widgetInstance)->getHeading(),
            default => str($widget)
                ->afterLast('\\')
                ->headline()
                ->toString(),
        };
    }

    private static function hasValidHeading(Widget $widgetInstance): bool
    {
        return $widgetInstance instanceof Widget // @phpstan-ignore-line
            && method_exists($widgetInstance, 'getHeading')
            && filled(invade($widgetInstance)->getHeading());
    }

    protected function getDefaultPermissionIdentifier(string $resource): string
    {
        return Str::of($resource)
            ->afterLast('Resources\\')
            ->beforeLast('Resource')
            ->replace('\\', '')
            ->snake()
            ->replace('_', '::');
    }

    protected static function getWidgetInstanceFromWidgetConfiguration(string | WidgetConfiguration $widget): string
    {
        return $widget instanceof WidgetConfiguration
            ? $widget->widget
            : $widget;
    }

    public function getAllResourcePermissions(): array
    {
        return collect($this->getResources())
            ->map(function (array $resourceEntity): array {
                return collect(
                    Utils::getResourcePermissionPrefixes($resourceEntity['fqcn'])
                )
                    ->flatMap(function (string $permission) use ($resourceEntity): array {
                        $name = $permission . '_' . $resourceEntity['resource'];
                        $permissionLabel = FilamentShieldPlugin::get()->hasLocalizedPermissionLabels()
                            ? str(static::getLocalizedResourcePermissionLabel($permission))
                                ->prepend(
                                    str($resourceEntity['fqcn']::getPluralModelLabel())
                                        ->title()
                                        ->append(' - ')
                                        ->toString()
                                )
                                ->toString()
                            : $name;

                        return [
                            $name => $permissionLabel,
                        ];
                    })
                    ->toArray();
            })
            ->sortKeys()
            ->collapse()
            ->toArray();
    }

    public static function getLocalizedOrFormattedCustomPermissionLabel(string $permission): string
    {
        return Lang::has("shield-permissions.$permission")
            ? __("shield-permissions.$permission")
            : Str::of($permission)->headline()->toString();
    }

    /** @return array<string, string> */
    public static function getCustomPermissions(bool $localizedOrFormatted = false): array
    {
        return collect(Utils::getCustomPermissions())
            ->mapWithKeys(function (string $label, int | string $key) use ($localizedOrFormatted): array {
                $permission = is_numeric($key) ? $label : $key;

                return [
                    Str::of($permission)->snake()->toString() => $localizedOrFormatted
                        ? static::getLocalizedOrFormattedCustomPermissionLabel($permission)
                        : Str::of($label)->headline()->toString(),
                ];
            })
            ->toArray();
    }

    protected function getEntitiesPermissions(): ?array
    {
        return collect($this->getAllResourcePermissions())->keys()
            ->merge(collect($this->getPages())->map->permission->keys())
            ->merge(collect($this->getWidgets())->map->permission->keys())
            ->merge(collect($this->getCustomPermissions())->keys())
            ->values()
            ->unique()
            ->toArray();
    }

    /**
     * Indicate if destructive Shield commands should be prohibited.
     * Prohibits: shield:setup, shield:install, and shield:generate
     */
    public static function prohibitDestructiveCommands(bool $prohibit = true): void
    {
        GenerateCommand::prohibit($prohibit);
        InstallCommand::prohibit($prohibit);
        PublishCommand::prohibit($prohibit);
        SetupCommand::prohibit($prohibit);
    }
}
