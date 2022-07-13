<?php

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Contracts\HasPermissions;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

trait HasPermissionsSchemaForm
{
    /*
    |--------------------------------------------------------------------------
    | Resource related logic
    |--------------------------------------------------------------------------
    */

    /**
     * Gets the filament resources that should have permissions.
     *
     * @return array|null
     */
    protected static function getResourceEntities(): ?array
    {
        return collect(Filament::getResources())
            ->filter(
                fn($resource) => in_array(HasPermissions::class, class_implements($resource))
            )
            ->reduce(
                function ($roles, $resource) {
                    $role = Str::lower(Str::before(Str::afterLast($resource, '\\'), 'Resource'));
                    $roles[$role] = $resource;

                    return $roles;
                },
                []
            );
    }

    /**
     * Get the grouping schema per resource.
     *
     * @return array|null
     */
    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(static::getResourceEntities())
            ->reduce(
                function ($entities, $resource, $entity) {
                    $entities[] = Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\Toggle::make($entity)
                                ->onIcon('heroicon-s-lock-open')
                                ->offIcon('heroicon-s-lock-closed')
                                ->reactive()
                                ->label(__(Str::headline($entity)))
                                ->afterStateUpdated(
                                    function (Closure $set, Closure $get, $state) use ($entity, $resource) {
                                        collect($resource::permissions())->each(
                                            function ($permission) use ($set, $entity, $state) {
                                                $set($permission.'_'.$entity, $state);
                                            }
                                        );

                                        if (!$state) {
                                            $set('select_all', false);
                                        }

                                        static::refreshSelectAllStateViaEntities($set, $get);
                                    }
                                )
                                ->dehydrated(false),

                            Forms\Components\Fieldset::make(__('filament-shield::filament-shield.field.permissions'))
                                ->extraAttributes(
                                    ['class' => 'text-primary-600', 'style' => 'border-color:var(--primary)']
                                )
                                ->columns([
                                    'default' => 2,
                                    'xl' => 2,
                                ])
                                ->schema(static::getResourceEntityPermissionsSchema($entity, $resource)),
                        ])
                        ->columns(2)
                        ->columnSpan(1);

                    return $entities;
                },
                []
            );
    }

    /**
     * Get the permissions of the resource.
     *
     * @param $entity
     * @param $resource
     *
     * @return array|null
     */
    public static function getResourceEntityPermissionsSchema($entity, $resource): ?array
    {
        return collect($resource::permissions())
            ->reduce(
                function ($permissions, $permission) use ($entity, $resource) {
                    $permissions[] = Forms\Components\Checkbox::make($permission.'_'.$entity)
                        ->label(__('filament-shield::filament-shield.prefixes.resources.'.$permission))
                        ->extraAttributes(['class' => 'text-primary-600'])
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use ($entity, $permission) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($permission.'_'.$entity, $record->checkPermissionTo($permission.'_'.$entity));

                            static::refreshResourceEntityStateAfterHydrated($record, $set, $entity);

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, Closure $get, $state) use ($entity, $resource) {
                            static::refreshResourceEntityStateAfterUpdate($set, $get, Str::of($entity), $resource);

                            if (!$state) {
                                $set($entity, false);
                                $set('select_all', false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state);

                    return $permissions;
                },
                []
            );
    }

    /*
    |--------------------------------------------------------------------------
    | Page related logic
    |--------------------------------------------------------------------------
    */


    protected static function getPageEntities(): ?array
    {
        return collect(Filament::getPages())
            ->filter(function ($page) {
                if (config('filament-shield.exclude.enabled')) {
                    return !in_array(Str::afterLast($page, '\\'), config('filament-shield.exclude.pages'));
                }

                return true;
            })
            ->reduce(function ($transformedPages, $page) {
                $name = Str::of($page)
                    ->after('Pages\\')
                    ->replace('\\', '')
                    ->snake()
                    ->prepend(
                        config('filament-shield.prefixes.page').'_'
                    );

                $transformedPages["{$name}"] = "{$name}";

                return $transformedPages;
            }, []);
    }

    protected static function getPageEntityPermissionsSchema(): ?array
    {
        return collect(static::getPageEntities())->reduce(function ($pages, $page) {
            $entity = Str::of($page)->after(config('filament-shield.prefixes.page').'_');

            $pages[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($page)
                        ->label(__('app.shield.pages.'.$entity))
                        ->inline()
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use ($page) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($page, $record->checkPermissionTo($page));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                            if (!$state) {
                                $set('select_all', false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state),
                ])
                ->columns(1)
                ->columnSpan(1);

            return $pages;
        }, []);
    }

    /*
    |--------------------------------------------------------------------------
    | Widget related logic
    |--------------------------------------------------------------------------
    */

    protected static function getWidgetEntities(): ?array
    {
        return collect(Filament::getWidgets())
            ->filter(function ($widget) {
                if (config('filament-shield.exclude.enabled')) {
                    return !in_array(Str::afterLast($widget, '\\'), config('filament-shield.exclude.widgets'));
                }

                return true;
            })
            ->reduce(function ($widgets, $widget) {
                $name = Str::of($widget)->after('Widgets\\')->replace('\\', '')->snake()->prepend(
                    config('filament-shield.prefixes.widget').'_'
                );
                $widgets["{$name}"] = "{$name}";

                return $widgets;
            }, []);
    }

    protected static function getWidgetEntityPermissionSchema(): ?array
    {
        return collect(static::getWidgetEntities())->reduce(function ($widgets, $widget) {
            $widgets[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($widget)
                        ->label(Str::of($widget)->after(config('filament-shield.prefixes.widget').'_')->headline())
                        ->inline()
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use ($widget) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($widget, $record->checkPermissionTo($widget));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                            if (!$state) {
                                $set('select_all', false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state),
                ])
                ->columns(1)
                ->columnSpan(1);

            return $widgets;
        }, []);
    }

    /*
    |--------------------------------------------------------------------------
    | Custom permissions related logic
    |--------------------------------------------------------------------------
    */

    protected static function getCustomEntities(): ?Collection
    {
        $resourcePermissions = collect();

        collect(static::getResourceEntities())
            ->each(
                function ($resource, $entity) use ($resourcePermissions) {
                    collect(config('filament-shield.prefixes.resource'))->map(
                        function ($permission) use ($resourcePermissions, $entity) {
                            $resourcePermissions->push((string)Str::of($permission.'_'.$entity));
                        }
                    );
                }
            );

        $entitiesPermissions = $resourcePermissions
            ->merge(static::getPageEntities())
            ->merge(static::getWidgetEntities())
            ->values();

        return Permission::query()
            ->whereNotIn('name', $entitiesPermissions)
            ->pluck('name');
    }

    protected static function getCustomEntitiesPermissionSchema(): ?array
    {
        return collect(static::getCustomEntities())
            ->reduce(
                function ($customEntities, $customPermission) {
                    $customEntities[] = Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Checkbox::make($customPermission)
                                ->label(Str::of($customPermission)->headline())
                                ->inline()
                                ->afterStateHydrated(
                                    function (Closure $set, Closure $get, $record) use ($customPermission) {
                                        if (is_null($record)) {
                                            return;
                                        }

                                        $set($customPermission, $record->checkPermissionTo($customPermission));

                                        static::refreshSelectAllStateViaEntities($set, $get);
                                    }
                                )
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                    if (!$state) {
                                        $set('select_all', false);
                                    }

                                    static::refreshSelectAllStateViaEntities($set, $get);
                                })
                                ->dehydrated(fn($state): bool => $state),
                        ])
                        ->columns(1)
                        ->columnSpan(1);

                    return $customEntities;
                },
                []
            );
    }

    /*
    |--------------------------------------------------------------------------
    | State related logic
    |--------------------------------------------------------------------------
    */

    /**
     * Refreshes the state of select all from the entities state.
     *
     * @param  Closure  $set
     * @param  Closure  $get
     *
     * @return void
     */
    protected static function refreshSelectAllStateViaEntities(Closure $set, Closure $get): void
    {
        $entitiesStates = collect(static::getResourceEntities())
            ->when(
                config('filament-shield.entities.pages'),
                fn($entities) => $entities->merge(static::getPageEntities())
            )
            ->when(
                config('filament-shield.entities.widgets'),
                fn($entities) => $entities->merge(static::getWidgetEntities())
            )
            ->when(
                config('filament-shield.entities.custom_permissions'),
                fn($entities) => $entities->merge(static::getCustomEntities())
            )
            ->map(function ($resource, $entity) use ($get) {
                return (bool)$get($entity);
            });

        if ($entitiesStates->containsStrict(false) === false) {
            $set('select_all', true);
        }

        if ($entitiesStates->containsStrict(false) === true) {
            $set('select_all', false);
        }
    }

    /**
     * Refreshes the state of all entities from the all select.
     *
     * @param  Closure  $set
     * @param           $state
     *
     * @return void
     */
    protected static function refreshEntitiesStatesViaSelectAll(Closure $set, $state): void
    {
        collect(static::getResourceEntities())->each(function ($resource, $entity) use ($set, $state) {
            $set($entity, $state);
            collect($resource::permissions())->each(
                function ($permission) use ($entity, $set, $state) {
                    $set($permission.'_'.$entity, $state);
                }
            );
        });

        collect(static::getPageEntities())->each(function ($page) use ($set, $state) {
            if (config('filament-shield.entities.pages')) {
                $set($page, $state);
            }
        });

        collect(static::getWidgetEntities())->each(function ($widget) use ($set, $state) {
            $set($widget, $state);
        });

        static::getCustomEntities()->each(function ($custom) use ($set, $state) {
            if (config('filament-shield.entities.custom_permissions')) {
                $set($custom, $state);
            }
        });
    }

    /**
     * Refreshes the state of toggle entities after updating an entity permission.
     *
     * @param  Closure  $set
     * @param  Closure  $get
     * @param  string  $entity
     * @param           $resource
     *
     * @return void
     */
    protected static function refreshResourceEntityStateAfterUpdate(
        Closure $set,
        Closure $get,
        string $entity,
        $resource
    ): void {
        $permissionStates = collect($resource::permissions())
            ->map(function ($permission) use ($get, $entity) {
                return (bool)$get($permission.'_'.$entity);
            });

        if ($permissionStates->containsStrict(false) === false) {
            $set($entity, true);
        }

        if ($permissionStates->containsStrict(false) === true) {
            $set($entity, false);
        }
    }

    /**
     * Refreshes the state of the resources in the edition.
     *
     * @param  Model  $record
     * @param  Closure  $set
     * @param  string  $entity
     *
     * @return void
     */
    protected static function refreshResourceEntityStateAfterHydrated(Model $record, Closure $set, string $entity): void
    {
        $entities = $record->permissions
            ->pluck('name')
            ->reduce(
                function ($entities, $entity) {
                    $entities[$entity] = Str::afterLast($entity, '_');

                    return $entities;
                },
                collect()
            )
            ->groupBy(fn($item) => $item)
            ->map
            ->count()
            ->reduce(
                function ($counts, $counted, $entity) {
                    if ($counted > 1 && $counted = count(config('filament-shield.prefixes.resource'))) {
                        $counts[$entity] = true;
                    } else {
                        $counts[$entity] = false;
                    }

                    return $counts;
                },
                []
            );

        // set entity's state if one are all permissions are true
        if (in_array($entity, array_keys($entities)) && $entities[$entity]) {
            $set($entity, true);
        } else {
            $set($entity, false);
            $set('select_all', false);
        }
    }
}
