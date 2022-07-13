<?php

namespace BezhanSalleh\FilamentShield\Traits;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Filament\Forms;

trait HasPermissionsSchemaForm
{
    /**--------------------------------*
    | Resource Related Logic Start     |
     *----------------------------------*/

    protected static function getResourceEntities(): ?array
    {
        return collect(Filament::getResources())
            ->filter(function ($resource) {
                if (config('filament-shield.exclude.enabled')) {
                    return !in_array(Str::of($resource)->afterLast('\\'), config('filament-shield.exclude.resources'));
                }
                return true;
            })
            ->reduce(function ($roles, $resource) {
                $role = Str::lower(Str::before(Str::afterLast($resource, '\\'), 'Resource'));
                $roles[$role] = $role;
                return $roles;
            }, []);
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(static::getResourceEntities())->reduce(function($entities,$entity) {
            $entities[] = Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Toggle::make($entity)
                        ->onIcon('heroicon-s-lock-open')
                        ->offIcon('heroicon-s-lock-closed')
                        ->reactive()
                        ->label(__(Str::headline($entity)))
                        ->afterStateUpdated(function (Closure $set,Closure $get, $state) use($entity) {

                            collect(config('filament-shield.prefixes.resource'))->each(function ($permission) use($set, $entity, $state) {
                                $set($permission.'_'.$entity, $state);
                            });

                            if (! $state) {
                                $set('select_all',false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(false)
                    ,
                    Forms\Components\Fieldset::make('Permissions')
                        ->extraAttributes(['class' => 'text-primary-600','style' => 'border-color:var(--primary)'])
                        ->columns([
                            'default' => 2,
                            'xl' => 2
                        ])
                        ->schema(static::getResourceEntityPermissionsSchema($entity))
                ])
                ->columns(2)
                ->columnSpan(1);
            return $entities;
        },[]);
    }

    public static function getResourceEntityPermissionsSchema($entity): ?array
    {
        return collect(config('filament-shield.prefixes.resource'))->reduce(function ($permissions, $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($permission.'_'.$entity)
                ->label(__('filament-shield::filament-shield.prefixes.resources.'.$permission))
                ->extraAttributes(['class' => 'text-primary-600'])
                ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($entity, $permission) {
                    if (is_null($record)) return;

                    $set($permission.'_'.$entity, $record->checkPermissionTo($permission.'_'.$entity));

                    static::refreshResourceEntityStateAfterHydrated($record, $set, $entity);

                    static::refreshSelectAllStateViaEntities($set, $get);
                })
                ->reactive()
                ->afterStateUpdated(function (Closure $set, Closure $get, $state) use($entity){

                    static::refreshResourceEntityStateAfterUpdate($set, $get, Str::of($entity));

                    if(!$state) {
                        $set($entity,false);
                        $set('select_all',false);
                    }

                    static::refreshSelectAllStateViaEntities($set, $get);
                })
                ->dehydrated(fn($state): bool => $state);
            return $permissions;
        },[]);
    }

    protected static function refreshSelectAllStateViaEntities(Closure $set, Closure $get): void
    {
        $entitiesStates = collect(static::getResourceEntities())
            ->when(config('filament-shield.entities.pages'), fn($entities) => $entities->merge(static::getPageEntities()))
            ->when(config('filament-shield.entities.widgets'), fn($entities) => $entities->merge(static::getWidgetEntities()))
            ->when(config('filament-shield.entities.custom_permissions'), fn($entities) => $entities->merge(static::getCustomEntities()))
            ->map(function ($entity) use($get) {
                return (bool) $get($entity);
            });

        if ($entitiesStates->containsStrict(false) === false) {
            $set('select_all', true);
        }

        if ($entitiesStates->containsStrict(false) === true) {
            $set('select_all', false);
        }
    }

    protected static function refreshEntitiesStatesViaSelectAll(Closure $set, $state): void
    {
        collect(static::getResourceEntities())->each(function($entity) use($set, $state) {
            $set($entity, $state);
            collect(config('filament-shield.prefixes.resource'))->each(function($permission) use($entity, $set, $state) {
                $set($permission.'_'.$entity, $state);
            });
        });

        collect(static::getPageEntities())->each(function($page) use($set, $state) {
            if(config('filament-shield.entities.pages')) {
                $set($page, $state);
            }
        });

        collect(static::getWidgetEntities())->each(function ($widget) use($set, $state) {
            $set($widget, $state);
        });

        static::getCustomEntities()->each(function ($custom) use ($set, $state) {
            if(config('filament-shield.entities.custom_permissions')) {
                $set($custom, $state);
            }
        });
    }

    protected static function refreshResourceEntityStateAfterUpdate(Closure $set, Closure $get, string $entity): void
    {
        $permissionStates = collect(config('filament-shield.prefixes.resource'))
            ->map(function($permission) use($get, $entity) {
                return (bool) $get($permission.'_'.$entity);
            });

        if ($permissionStates->containsStrict(false) === false) {
            $set($entity, true);
        }

        if ($permissionStates->containsStrict(false) === true) {
            $set($entity, false);
        }
    }

    protected static function refreshResourceEntityStateAfterHydrated(Model $record, Closure $set, string $entity): void
    {
        $entities = $record->permissions->pluck('name')
            ->reduce(function ($roles, $role){
                $roles[$role] = Str::afterLast($role, '_');
                return $roles;
            },collect())
            ->values()
            ->groupBy(function ($item) {
                return $item;
            })->map->count()
            ->reduce(function ($counts,$role,$key) {
                if ($role > 1 && $role = count(config('filament-shield.prefixes.resource'))) {
                    $counts[$key] = true;
                }else {
                    $counts[$key] = false;
                }
                return $counts;
            },[]);

        // set entity's state if one are all permissions are true
        if (in_array($entity,array_keys($entities)) && $entities[$entity])
        {
            $set($entity, true);
        } else {
            $set($entity, false);
            $set('select_all', false);
        }
    }
    /**--------------------------------*
    | Resource Related Logic End       |
     *----------------------------------*/

    /**--------------------------------*
    | Page Related Logic Start       |
     *----------------------------------*/
    protected static function getPageEntities(): ? array
    {
        return collect(Filament::getPages())
            ->filter(function ($page) {
                if (config('filament-shield.exclude.enabled')) {
                    return !in_array(Str::afterLast($page, '\\'), config('filament-shield.exclude.pages'));
                }
                return true;
            })
            ->reduce(function($transformedPages,$page) {
                $name = Str::of($page)->after('Pages\\')->replace('\\','')->snake()->prepend(config('filament-shield.prefixes.page').'_');
                $transformedPages["{$name}"] = "{$name}";
                return $transformedPages;
            },[]);
    }

    protected static function getPageEntityPermissionsSchema(): ?array
    {
        return collect(static::getPageEntities())->reduce(function($pages,$page) {
            $entity = Str::of($page)->after(config('filament-shield.prefixes.page').'_')->headline();

            $pages[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($page)
                        ->label(__($entity))
                        ->inline()
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($page) {
                            if (is_null($record)) return;

                            $set($page, $record->checkPermissionTo($page));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set,Closure $get, $state) {

                            if (! $state) {
                                $set('select_all',false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state)
                ])
                ->columns(1)
                ->columnSpan(1);
            return $pages;
        },[]);
    }
    /**--------------------------------*
    | Page Related Logic End          |
     *----------------------------------*/


    /**--------------------------------*
    | Widget Related Logic Start       |
     *----------------------------------*/
    protected static function getWidgetEntities(): ? array
    {
        return collect(Filament::getWidgets())
            ->filter(function ($widget) {
                if (config('filament-shield.exclude.enabled')) {
                    return !in_array(Str::afterLast($widget, '\\'), config('filament-shield.exclude.widgets'));
                }
                return true;
            })
            ->reduce(function($widgets,$widget) {
                $name = Str::of($widget)->after('Widgets\\')->replace('\\','')->snake()->prepend(config('filament-shield.prefixes.widget').'_');
                $widgets["{$name}"] = "{$name}";
                return $widgets;
            },[]);
    }

    protected static function getWidgetEntityPermissionSchema(): ?array
    {
        return collect(static::getWidgetEntities())->reduce(function($widgets,$widget) {
            $widgets[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($widget)
                        ->label(Str::of($widget)->after(config('filament-shield.prefixes.widget').'_')->headline())
                        ->inline()
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($widget) {
                            if (is_null($record)) return;

                            $set($widget, $record->checkPermissionTo($widget));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set,Closure $get, $state) {

                            if (! $state) {
                                $set('select_all',false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state)
                ])
                ->columns(1)
                ->columnSpan(1);
            return $widgets;
        },[]);
    }
    /**--------------------------------*
    | Widget Related Logic End          |
     *----------------------------------*/

    protected static function getCustomEntities(): ?Collection
    {
        $resourcePermissions = collect();
        collect(static::getResourceEntities())->each(function($entity) use($resourcePermissions){
            collect(config('filament-shield.prefixes.resource'))->map(function($permission) use($resourcePermissions, $entity) {
                $resourcePermissions->push((string) Str::of($permission.'_'.$entity));
            });
        });

        $entitiesPermissions = $resourcePermissions
            ->merge(static::getPageEntities())
            ->merge(static::getWidgetEntities())
            ->values();

        return Permission::whereNotIn('name',$entitiesPermissions)->pluck('name');
    }

    protected static function getCustomEntitiesPermisssionSchema(): ?array
    {
        return collect(static::getCustomEntities())->reduce(function($customEntities,$customPermission) {
            $customEntities[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($customPermission)
                        ->label(Str::of($customPermission)->headline())
                        ->inline()
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($customPermission) {
                            if (is_null($record)) return;

                            $set($customPermission, $record->checkPermissionTo($customPermission));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set,Closure $get, $state) {

                            if (! $state) {
                                $set('select_all',false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn($state): bool => $state)
                ])
                ->columns(1)
                ->columnSpan(1);
            return $customEntities;
        },[]);
    }
}
