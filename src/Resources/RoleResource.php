<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?int $navigationSort = -1;

    protected static ?string $slug = 'shield/roles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->required()
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (Closure $set, $state): string => $set('name', Str::lower($state))),
                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(config('filament.auth.guard'))
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (Closure $set, $state): string => $set('guard_name', Str::lower($state))),
                                Forms\Components\Toggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(__('filament-shield::filament-shield.field.select_all.message'))
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        static::refreshEntitiesStatesViaSelectAll($set, $state);
                                    })
                                    ->dehydrated(fn ($state): bool => $state),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Forms\Components\Section::make(__('filament-shield::filament-shield.section'))
                    ->schema([
                        Forms\Components\Tabs::make('Permissions')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                                    ->visible(fn (): bool => (bool) config('filament-shield.entities.resources'))
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                        ->schema(static::getResourceEntitiesSchema())
                                        ->columns([
                                            'sm' => 2,
                                            'lg' => 3,
                                        ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.pages'))
                                    ->visible(fn (): bool => (bool) (config('filament-shield.entities.pages') && count(FilamentShield::getPages())) > 0 ? true : false)
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getPageEntityPermissionsSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                                    ->visible(fn (): bool => (bool) (config('filament-shield.entities.widgets') && count(FilamentShield::getWidgets())) > 0 ? true : false)
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getWidgetEntityPermissionSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ]),
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                                    ->visible(fn (): bool => (bool) config('filament-shield.entities.custom_permissions'))
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getCustomEntitiesPermisssionSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ]),
                                    ]),
                            ])
                            ->columnSpan('full'),
                    ]),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('name')
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('guard_name')
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                Tables\Columns\BadgeColumn::make('permissions_count')
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-shield::filament-shield.column.updated_at'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'settings' => Pages\ViewShieldSettings::route('/settings'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    protected static function getNavigationGroup(): ?string
    {
        return __('filament-shield::filament-shield.nav.group');
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    protected static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    /**--------------------------------*
    | Resource Related Logic Start     |
    *----------------------------------*/

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(FilamentShield::getResources())->sortKeys()->reduce(function ($entities, $entity) {
            $entities[] = Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Toggle::make($entity)
                            ->label(FilamentShield::getLocalizedResourceLabel($entity))
                            ->onIcon('heroicon-s-lock-open')
                            ->offIcon('heroicon-s-lock-closed')
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, Closure $get, $state) use ($entity) {
                                collect(config('filament-shield.prefixes.resource'))->each(function ($permission) use ($set, $entity, $state) {
                                    $set($permission.'_'.$entity, $state);
                                });

                                if (! $state) {
                                    $set('select_all', false);
                                }

                                static::refreshSelectAllStateViaEntities($set, $get);
                            })
                            ->dehydrated(false)
                            ,
                        Forms\Components\Fieldset::make('Permissions')
                        ->label(__('filament-shield::filament-shield.column.permissions'))
                        ->extraAttributes(['class' => 'text-primary-600','style' => 'border-color:var(--primary)'])
                        ->columns([
                            'default' => 2,
                            'xl' => 2,
                        ])
                        ->schema(static::getResourceEntityPermissionsSchema($entity)),
                    ])
                    ->columns(2)
                    ->columnSpan(1);

            return $entities;
        }, []);
    }

    public static function getResourceEntityPermissionsSchema($entity): ?array
    {
        return collect(config('filament-shield.prefixes.resource'))->reduce(function ($permissions, $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($permission.'_'.$entity)
                ->label(FilamentShield::getLocalizedResourcePermissionLabel($permission))
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
                ->afterStateUpdated(function (Closure $set, Closure $get, $state) use ($entity) {
                    static::refreshResourceEntityStateAfterUpdate($set, $get, Str::of($entity));

                    if (! $state) {
                        $set($entity, false);
                        $set('select_all', false);
                    }

                    static::refreshSelectAllStateViaEntities($set, $get);
                })
                ->dehydrated(fn ($state): bool => $state);

            return $permissions;
        }, []);
    }

    protected static function refreshSelectAllStateViaEntities(Closure $set, Closure $get): void
    {
        $entitiesStates = collect(FilamentShield::getResources())
            ->when(config('filament-shield.entities.pages'), fn ($entities) => $entities->merge(FilamentShield::getPages()))
            ->when(config('filament-shield.entities.widgets'), fn ($entities) => $entities->merge(FilamentShield::getWidgets()))
            ->when(config('filament-shield.entities.custom_permissions'), fn ($entities) => $entities->merge(static::getCustomEntities()))
            ->map(function ($entity) use ($get) {
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
        collect(FilamentShield::getResources())->each(function ($entity) use ($set, $state) {
            $set($entity, $state);
            collect(config('filament-shield.prefixes.resource'))->each(function ($permission) use ($entity, $set, $state) {
                $set($permission.'_'.$entity, $state);
            });
        });

        collect(FilamentShield::getPages())->each(function ($page) use ($set, $state) {
            if (config('filament-shield.entities.pages')) {
                $set($page, $state);
            }
        });

        collect(FilamentShield::getWidgets())->each(function ($widget) use ($set, $state) {
            $set($widget, $state);
        });

        static::getCustomEntities()->each(function ($custom) use ($set, $state) {
            if (config('filament-shield.entities.custom_permissions')) {
                $set($custom, $state);
            }
        });
    }

    protected static function refreshResourceEntityStateAfterUpdate(Closure $set, Closure $get, string $entity): void
    {
        $permissionStates = collect(config('filament-shield.prefixes.resource'))
            ->map(function ($permission) use ($get, $entity) {
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
            ->reduce(function ($roles, $role) {
                $roles[$role] = Str::afterLast($role, '_');

                return $roles;
            }, collect())
            ->values()
            ->groupBy(function ($item) {
                return $item;
            })->map->count()
            ->reduce(function ($counts, $role, $key) {
                if ($role > 1 && $role = count(config('filament-shield.prefixes.resource'))) {
                    $counts[$key] = true;
                } else {
                    $counts[$key] = false;
                }

                return $counts;
            }, []);

        // set entity's state if one are all permissions are true
        if (in_array($entity, array_keys($entities)) && $entities[$entity]) {
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


    protected static function getPageEntityPermissionsSchema(): ?array
    {
        return collect(FilamentShield::getPages())->sortKeys()->reduce(function ($pages, $page) {
            $pages[] = Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Checkbox::make($page)
                            ->label(FilamentShield::getLocalizedPageLabel($page))
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
                                if (! $state) {
                                    $set('select_all', false);
                                }

                                static::refreshSelectAllStateViaEntities($set, $get);
                            })
                            ->dehydrated(fn ($state): bool => $state),
                    ])
                    ->columns(1)
                    ->columnSpan(1);

            return $pages;
        }, []);
    }
    /**--------------------------------*
    | Page Related Logic End          |
    *----------------------------------*/

    /**--------------------------------*
    | Widget Related Logic Start       |
    *----------------------------------*/


    protected static function getWidgetEntityPermissionSchema(): ?array
    {
        return collect(FilamentShield::getWidgets())->reduce(function ($widgets, $widget) {
            $widgets[] = Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Checkbox::make($widget)
                            ->label(FilamentShield::getLocalizedWidgetLabel($widget))
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
                                if (! $state) {
                                    $set('select_all', false);
                                }

                                static::refreshSelectAllStateViaEntities($set, $get);
                            })
                            ->dehydrated(fn ($state): bool => $state),
                    ])
                    ->columns(1)
                    ->columnSpan(1);

            return $widgets;
        }, []);
    }
    /**--------------------------------*
    | Widget Related Logic End          |
    *----------------------------------*/

    protected static function getCustomEntities(): ?Collection
    {
        $resourcePermissions = collect();
        collect(FilamentShield::getResources())->each(function ($entity) use ($resourcePermissions) {
            collect(config('filament-shield.prefixes.resource'))->map(function ($permission) use ($resourcePermissions, $entity) {
                $resourcePermissions->push((string) Str::of($permission.'_'.$entity));
            });
        });

        $entitiesPermissions = $resourcePermissions
            ->merge(FilamentShield::getPages())
            ->merge(FilamentShield::getWidgets())
            ->values();

        return Permission::whereNotIn('name', $entitiesPermissions)->pluck('name');
    }

    protected static function getCustomEntitiesPermisssionSchema(): ?array
    {
        return collect(static::getCustomEntities())->reduce(function ($customEntities, $customPermission) {
            $customEntities[] = Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Checkbox::make($customPermission)
                            ->label(Str::of($customPermission)->headline())
                            ->inline()
                            ->afterStateHydrated(function (Closure $set, Closure $get, $record) use ($customPermission) {
                                if (is_null($record)) {
                                    return;
                                }

                                $set($customPermission, $record->checkPermissionTo($customPermission));

                                static::refreshSelectAllStateViaEntities($set, $get);
                            })
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                                if (! $state) {
                                    $set('select_all', false);
                                }

                                static::refreshSelectAllStateViaEntities($set, $get);
                            })
                            ->dehydrated(fn ($state): bool => $state),
                    ])
                    ->columns(1)
                    ->columnSpan(1);

            return $customEntities;
        }, []);
    }
}
