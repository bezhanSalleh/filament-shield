<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use BezhanSalleh\FilamentShield\Support\ShieldConfig;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class FilamentShield
{
    use EvaluatesClosures;

    protected ?Closure $buildPermissionKeyUsing = null;

    public function buildPermissionKeyUsing(Closure $callback): static
    {
        $this->buildPermissionKeyUsing = $callback;

        return $this;
    }

    public function generateForResource(string $resourceKey): void
    {
        if (Utils::isResourceEntityEnabled()) {
            $permissions = collect($this->getResourcePermissions($resourceKey))
                ->map(
                    fn (string $permission): string => Utils::getPermissionModel()::firstOrCreate(
                        ['name' => $permission],
                        ['guard_name' => Utils::getFilamentAuthGuard()]
                    )->name
                )
                ->toArray();
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

        if (Utils::isCustomPermissionEntityEnabled() && $customPermissions->isNotEmpty()) {
            $permissions = $customPermissions
                ->map(fn (string $permission): string => Utils::getPermissionModel()::firstOrCreate(
                    ['name' => $permission],
                    ['guard_name' => Utils::getFilamentAuthGuard()]
                )->name)
                ->toArray();
            static::giveSuperAdminPermission($permissions);
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
                return in_array($resource, ShieldConfig::init()->exclude->resources);
            })
            ->mapWithKeys(function (string $resource): array {
                $policyConfig = ShieldConfig::init()->policies;
                $methods = [];

                if (method_exists($resource, 'getPermissionPrefixes')) {
                    $methods = $resource::getPermissionPrefixes();
                }

                if ($policyConfig->merge) {
                    $methods = array_merge($methods, $policyConfig->methods);
                }

                $affixes = collect($methods)
                    ->map(fn ($affix) => $this->format('camel', $affix))
                    ->unique()
                    ->toArray();

                return [
                    $resource => [
                        'resourceFqcn' => $resource,
                        'model' => class_basename($resource::getModel()),
                        'modelFqcn' => str($resource::getModel())->toString(),
                        'permissions' => $this->getDefaultPermissionKeys($resource, $affixes),
                    ],
                ];
            })
            ->sortKeys()
            ->toArray();
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
    public function getPages(): ?array
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

                return in_array($page, ShieldConfig::init()->exclude->pages);
            })
            ->mapWithKeys(function (string $page): array {
                return [
                    $page => [
                        'pageFqcn' => $page,
                        'permissions' => $this->getDefaultPermissionKeys($page, ShieldConfig::init()->permissions->page->prefix),
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Transform filament widgets to key value pair for shield
     */
    public function getWidgets(): ?array
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
                return in_array(
                    needle: static::getWidgetInstanceFromWidgetConfiguration($widget),
                    haystack: array_values(ShieldConfig::init()->exclude->widgets)
                );
            })
            ->mapWithKeys(function (string | WidgetConfiguration $widget): array {
                return [
                    $widget => [
                        'widgetFqcn' => static::getWidgetInstanceFromWidgetConfiguration($widget),
                        'permissions' => $this->getDefaultPermissionKeys($widget, ShieldConfig::init()->permissions->widget->prefix),
                    ],
                ];
            })
            ->toArray();
    }

    private static function hasValidHeading(Widget $widgetInstance): bool
    {
        return $widgetInstance instanceof Widget // @phpstan-ignore-line
            && method_exists($widgetInstance, 'getHeading')
            && filled(invade($widgetInstance)->getHeading());
    }

    private function buildPermissionKey(string $entity, string $affix, string $subject): string
    {
        if ($this->buildPermissionKeyUsing instanceof \Closure) {
            $config = ShieldConfig::init()->permissions;

            /** @var string $result */
            $result = $this->evaluate(
                value: $this->buildPermissionKeyUsing,
                namedInjections: [
                    'entity' => $entity,
                    'affix' => $affix,
                    'subject' => $subject,
                    'case' => $config->case,
                    'separator' => $config->separator,
                ]
            );

            return $result;
        }

        // Default implementation
        $config = ShieldConfig::init()->permissions;

        return $this->format($config->case, $affix) . $config->separator . $this->format($config->case, $subject);
    }

    public function getDefaultPermissionKeys(string $entity, string | array $affixes): array
    {
        $subject = $this->resolveSubject($entity);

        if (is_array($affixes)) {
            return collect($affixes)
                ->mapWithKeys(fn (string $affix): array => [
                    $this->format('camel', $affix) => [
                        'key' => $this->buildPermissionKey($entity, $affix, $subject),
                        'label' => $this->getAffixLabel($affix) . ' ' . $this->resolveLabel($entity),
                    ],
                ])
                ->uniqueStrict()
                ->toArray();
        }

        return [$this->buildPermissionKey($entity, $affixes, $subject) => $this->resolveLabel($entity)];
    }

    protected function resolveLabel(string $entity): string
    {
        $entity = resolve($entity);

        return match (true) {
            $entity instanceof Resource => $this->getLocalizedResourceLabel($entity),
            $entity instanceof Page => $this->getLocalizedPageLabel($entity),
            $entity instanceof Widget => $this->getLocalizedWidgetLabel($entity),
            default => throw new InvalidArgumentException('Entity must be an instance of Resource, Page, or Widget.'),
        };
    }

    protected function resolveSubject(string $entity): string
    {
        $entity = resolve($entity);
        $permissionConfig = ShieldConfig::init()->permissions;

        $subject = match (true) {
            $entity instanceof Resource => $permissionConfig->resource->subject,
            $entity instanceof Page => $permissionConfig->page->subject,
            $entity instanceof Widget => $permissionConfig->widget->subject,
            default => throw new InvalidArgumentException('Entity must be an instance of Resource, Page, or Widget.'),
        };

        if ($subject === 'model' && method_exists($entity::class, 'getModel')) {
            return class_basename($entity::getModel());
        }

        return class_basename($entity);
    }

    protected function format(string $case, string $value): string
    {
        return match ($case) {
            'kebab' => Str::of($value)->kebab()->toString(),
            'pascal' => Str::of($value)->studly()->toString(),
            'upper_snake' => Str::of($value)->snake()->upper()->toString(),
            'lower_snake' => Str::of($value)->snake()->lower()->toString(),
            'camel' => Str::of($value)->camel()->toString(),
            default => Str::of($value)->snake()->toString(),
        };
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
            ->map(fn (array $resourceEntity): array => collect(
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
                ->toArray())
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

    public function getEntitiesPermissions(): ?array
    {
        return collect($this->getAllResourcePermissions())->keys()
            ->merge(collect(static::getPages())->map->permission->keys())
            ->merge(collect(static::getWidgets())->map->permission->keys())
            ->merge(collect(static::getCustomPermissions())->keys())
            ->values()
            ->flatten()
            ->unique()
            ->toArray();
    }

    // helpers

    public function getResourcePermissions(string $key): ?array
    {
        return array_values($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePolicyActions(string $key): ?array
    {
        return array_keys($this->getResourcePolicyActionsWithPermissions($key));
    }

    public function getResourcePolicyActionsWithPermissions(string $key): ?array
    {
        return collect(data_get(
            target: FilamentShield::getResources(),
            key: "$key.permissions"
        ))
            ->mapWithKeys(fn (array $permission, string $action): array => [$action => $permission['key']])
            ->toArray();
    }

    // prefixes

    public function getLocalizedResourceAffixes(): array
    {
        $config = ShieldConfig::init();

        return collect($config->policies->methods)
            ->mapWithKeys(function ($method) use ($config): array {
                $affix = Str::of($method)->snake()->toString();
                if ($config->permissions->localization->enabled) {
                    return [$method => __("{$config->permissions->localization->key}.{$affix}")];
                }

                return [$method => Str::of($method)->headline()->toString()];
            })
            ->toArray();
    }

    // Labels
    public function getAffixLabel(string $affix): string
    {
        return Arr::get(
            array: $this->getLocalizedResourceAffixes(),
            key: Str::of($affix)->camel()->toString(),
            default: Str::of($affix)->headline()->toString()
        );
    }

    public function getLocalizedResourceLabel(Resource $resource): string
    {
        return Str::of($resource::getModelLabel())->headline()->toString();
    }

    public function getLocalizedPageLabel(Page $page): string
    {
        return $page->getTitle()
                ?? $page->getHeading()
                ?? $page->getNavigationLabel()
                ?? __(Str::of(class_basename($page))->snake()->prepend('permissions.')->toString())
                ?? Str::of(class_basename($page))->headline()->toString();
    }

    public static function getLocalizedWidgetLabel(Widget $widget): string
    {
        return match (true) {
            $widget instanceof TableWidget => (string) invade($widget)->makeTable()->getHeading(), // @phpstan-ignore-line
            self::hasValidHeading($widget) => (string) invade($widget)->getHeading(),
            default => __(Str::of(class_basename($widget))->snake()->prepend('permissions.')->toString()) ?? str($widget)
                ->afterLast('\\')
                ->headline()
                ->toString(),
        };
    }

    public function getGeneratorOption(): string
    {
        $config = ShieldConfig::init();

        return match (true) {
            $config->permissions->generate && $config->policies->generate => 'policies_and_permissions',
            $config->permissions->generate => 'permissions',
            $config->policies->generate => 'policies',
            default => 'none',
        };
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
