<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Label;

class RoleResource extends Resource implements HasShieldPermissions
{
    protected static ?string $recordTitleAttribute = 'name';

    protected static $permissionsCollection;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('filament-shield::filament-shield.field.name'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),
                                Forms\Components\Toggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(fn (): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
                                    ->live()
                                    ->afterStateUpdated(function (Forms\Set $set, $state) {
                                        static::refreshEntitiesStatesViaSelectAll($set, $state);
                                    })
                                    ->dehydrated(fn ($state): bool => $state),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Forms\Components\Tabs::make('Permissions')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.panels'))
                            ->visible(fn (): bool => (bool) Utils::isPanelEntityEnabled() && count(FilamentShield::getPanels()))
                            ->live()
                            ->schema([
                                Forms\Components\Grid::make([
                                    'sm' => 2,
                                    'lg' => 3,
                                ])
                                    ->schema(static::getPanelEntityPermissionsSchema())
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 3,
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                            ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
                            ->live()
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
                            ->visible(fn (): bool => (bool) Utils::isPageEntityEnabled() && (count(FilamentShield::getPages()) > 0 ? true : false))
                            ->live()
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
                            ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && (count(FilamentShield::getWidgets()) > 0 ? true : false))
                            ->live()
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
                            ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled())
                            ->live()
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

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.name'))
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->colors(['primary'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.guard_name')),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
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
                Tables\Actions\DeleteAction::make(),
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
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    public static function getModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralModelLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.roles');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    public static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }

    public static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    public static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? static::getModel()::count()
            : null;
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    /**--------------------------------*
    | Resource Related Logic Start     |
    *----------------------------------*/

    public static function getResourceEntitiesSchema(): ?array
    {
        if (blank(static::$permissionsCollection)) {
            static::$permissionsCollection = Utils::getPermissionModel()::all();
        }

        return collect(FilamentShield::getResources())->sortKeys()->reduce(function ($entities, $entity) {
            $entities[] = Forms\Components\Section::make()
                ->extraAttributes(['class' => 'border-0 shadow-lg'])
                ->schema([
                    Forms\Components\Toggle::make($entity['resource'])
                        ->label(FilamentShield::getLocalizedResourceLabel($entity['fqcn']))
                        ->helperText(Utils::showModelPath($entity['fqcn']))
                        ->onIcon('heroicon-s-lock-open')
                        ->offIcon('heroicon-s-lock-closed')
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) use ($entity) {
                            collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->each(function ($permission) use ($set, $entity, $state) {
                                $set($permission . '_' . $entity['resource'], $state);
                            });

                            if (! $state) {
                                $set('select_all', false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(false),
                    Forms\Components\Fieldset::make('Permissions')
                        ->label(__('filament-shield::filament-shield.column.permissions'))
                        ->extraAttributes(['class' => 'text-primary-600'])
                        ->columns([
                            'default' => 2,
                            'xl' => 2,
                        ])
                        ->schema(static::getResourceEntityPermissionsSchema($entity)),
                ])
                ->columnSpan(1);

            return $entities;
        }, collect())
            ?->toArray() ?? [];
    }

    public static function getResourceEntityPermissionsSchema($entity): ?array
    {
        return collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->reduce(function ($permissions /** @phpstan ignore-line */, $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($permission . '_' . $entity['resource'])
                ->label(FilamentShield::getLocalizedResourcePermissionLabel($permission))
                ->extraAttributes(['class' => 'text-primary-600'])
                ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) use ($entity, $permission) {
                    if (is_null($record)) {
                        return;
                    }

                    $set($permission . '_' . $entity['resource'], $record->checkPermissionTo($permission . '_' . $entity['resource']));

                    static::refreshResourceEntityStateAfterHydrated($record, $set, $entity);

                    static::refreshSelectAllStateViaEntities($set, $get);
                })
                ->live()
                ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) use ($entity) {
                    static::refreshResourceEntityStateAfterUpdate($set, $get, $entity);

                    if (! $state) {
                        $set($entity['resource'], false);
                        $set('select_all', false);
                    }

                    static::refreshSelectAllStateViaEntities($set, $get);
                })
                ->dehydrated(fn ($state): bool => $state);

            return $permissions;
        }, collect())
            ->toArray();
    }

    protected static function refreshSelectAllStateViaEntities(Forms\Set $set, Forms\Get $get): void
    {
        $entitiesStates = collect(FilamentShield::getResources())
            ->when(Utils::isPanelEntityEnabled(), fn ($entities) => $entities->merge(FilamentShield::getPanels()))
            ->when(Utils::isPageEntityEnabled(), fn ($entities) => $entities->merge(FilamentShield::getPages()))
            ->when(Utils::isWidgetEntityEnabled(), fn ($entities) => $entities->merge(FilamentShield::getWidgets()))
            ->when(Utils::isCustomPermissionEntityEnabled(), fn ($entities) => $entities->merge(static::getCustomEntities()))
            ->map(function ($entity) use ($get) {
                if (is_array($entity)) {
                    return (bool) $get($entity['resource']);
                }

                return (bool) $get($entity);
            });

        if ($entitiesStates->containsStrict(false) === false) {
            $set('select_all', true);
        }

        if ($entitiesStates->containsStrict(false) === true) {
            $set('select_all', false);
        }
    }

    protected static function refreshEntitiesStatesViaSelectAll(Forms\Set $set, $state): void
    {
        collect(FilamentShield::getResources())->each(function ($entity) use ($set, $state) {
            $set($entity['resource'], $state);
            collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->each(function ($permission) use ($entity, $set, $state) {
                $set($permission . '_' . $entity['resource'], $state);
            });
        });

        collect(FilamentShield::getPages())->each(function ($page) use ($set, $state) {
            if (Utils::isPageEntityEnabled()) {
                $set($page, $state);
            }
        });

        collect(FilamentShield::getWidgets())->each(function ($widget) use ($set, $state) {
            if (Utils::isWidgetEntityEnabled()) {
                $set($widget, $state);
            }
        });

        static::getCustomEntities()->each(function ($custom) use ($set, $state) {
            if (Utils::isCustomPermissionEntityEnabled()) {
                $set($custom, $state);
            }
        });
    }

    protected static function refreshResourceEntityStateAfterUpdate(Forms\Set $set, Forms\Get $get, array $entity): void
    {
        $permissionStates = collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->map(function ($permission) use ($get, $entity) {
                return (bool) $get($permission . '_' . $entity['resource']);
            });

        if ($permissionStates->containsStrict(false) === false) {
            $set($entity['resource'], true);
        }

        if ($permissionStates->containsStrict(false) === true) {
            $set($entity['resource'], false);
        }
    }

    protected static function refreshResourceEntityStateAfterHydrated(Model $record, Forms\Set $set, array $entity): void
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
            ->reduce(function ($counts, $role, $key) use ($entity) {
                $count = count(Utils::getResourcePermissionPrefixes($entity['fqcn']));
                if ($role > 1 && $role === $count) {
                    $counts[$key] = true;
                } else {
                    $counts[$key] = false;
                }

                return $counts;
            }, []);

        // set entity's state if one are all permissions are true
        if (Arr::exists($entities, $entity['resource']) && Arr::get($entities, $entity['resource'])) {
            $set($entity['resource'], true);
        } else {
            $set($entity['resource'], false);
            $set('select_all', false);
        }
    }
    /**--------------------------------*
    | Resource Related Logic End       |
    *----------------------------------*/

    /**--------------------------------*
    | Panel Related Logic Start       |
    *----------------------------------*/

    protected static function getPanelEntityPermissionsSchema(): ?array
    {
        return collect(FilamentShield::getPanels())->sortKeys()->reduce(function ($panels, $panel) {
            $panels[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($panel)
                        ->label(FilamentShield::getLocalizedPanelLabel($panel))
                        ->inline()
                        ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) use ($panel) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($panel, $record->checkPermissionTo($panel));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
                            if (! $state) {
                                $set('select_all', false);
                            }

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->dehydrated(fn ($state): bool => $state),
                ])
                ->columns(1)
                ->columnSpan(1);

            return $panels;
        }, []);
    }
    /**--------------------------------*
    | Panel Related Logic End          |
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
                        ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) use ($page) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($page, $record->checkPermissionTo($page));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
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
                        ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) use ($widget) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($widget, $record->checkPermissionTo($widget));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
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
            collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->map(function ($permission) use ($resourcePermissions, $entity) {
                $resourcePermissions->push((string) Str::of($permission . '_' . $entity['resource']));
            });
        });

        $entitiesPermissions = $resourcePermissions
            ->merge(FilamentShield::getPanels())
            ->merge(FilamentShield::getPages())
            ->merge(FilamentShield::getWidgets())
            ->values();

        return static::$permissionsCollection->whereNotIn('name', $entitiesPermissions)->pluck('name');
    }

    protected static function getCustomEntitiesPermisssionSchema(): ?array
    {
        return collect(static::getCustomEntities())->reduce(function ($customEntities, $customPermission) {
            $customEntities[] = Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Checkbox::make($customPermission)
                        ->label(Str::of($customPermission)->headline())
                        ->inline()
                        ->afterStateHydrated(function (Forms\Set $set, Forms\Get $get, $record) use ($customPermission) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($customPermission, $record->checkPermissionTo($customPermission));

                            static::refreshSelectAllStateViaEntities($set, $get);
                        })
                        ->live()
                        ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, $state) {
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
