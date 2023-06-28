<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

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

    protected static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    protected static function getNavigationGroup(): ?string
    {
        return Utils::isResourceNavigationGroupEnabled()
            ? __('filament-shield::filament-shield.nav.group')
            : '';
    }

    protected static function getNavigationLabel(): string
    {
        return __('filament-shield::filament-shield.nav.role.label');
    }

    protected static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    protected static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    protected static function getNavigationBadge(): ?string
    {
        return Utils::isResourceNavigationBadgeEnabled()
            ? static::getModel()::count()
            : null;
    }

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
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->nullable()
                                    ->maxLength(255),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                Forms\Components\Tabs::make('Permissions')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                            ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
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
                            ->schema([
                                Forms\Components\Grid::make([
                                    'sm' => 3,
                                    'lg' => 4,
                                ])
                                    ->schema([
                                        CheckboxList::make('pages')
                                            ->bulkToggleable()
                                            ->columns(5)
                                            ->afterStateHydrated(fn (Closure $set, Closure $get, $record) => static::setPermissionsStates(FilamentShield::getPageOptions(), $set, 'pages', $record))
                                            ->options(FilamentShield::getPageOptions()),
                                    ])
                                    ->columns([
                                        'sm' => 1,
                                        'lg' => 1,
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                            ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && (count(FilamentShield::getWidgets()) > 0 ? true : false))
                            ->schema([
                                Forms\Components\Grid::make([
                                    'sm' => 3,
                                    'lg' => 4,
                                ])
                                    ->schema([
                                        CheckboxList::make('widgets')
                                            ->bulkToggleable()
                                            ->columns(5)
                                            ->afterStateHydrated(fn (Closure $set, Closure $get, $record) => static::setPermissionsStates(FilamentShield::geWidgetOptions(), $set, 'widgets', $record))
                                            ->options(FilamentShield::geWidgetOptions()),
                                    ])
                                    ->columns([
                                        'sm' => 1,
                                        'lg' => 1,
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                            ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled())
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
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }

    private static function setPermissionsStates($permissions, $set, $key, $record): void
    {
        if (is_null($record)) {
            return;
        }

        $enabledPermissions = [];

        foreach ($permissions as $p => $label) {
            if ($record->checkPermissionTo($p)) {
                $enabledPermissions[] = $p;
            }
        }

        $set($key, $enabledPermissions);
    }

    /**--------------------------------*
    | Resource Related Logic Start     |
    *----------------------------------*/

    public static function getResourceEntitiesSchema(): ?array
    {
        if (blank(static::$permissionsCollection)) {
            static::$permissionsCollection = Utils::getPermissionModel()::all();
        }

        return collect(FilamentShield::getResources())->sortKeys()->reduce(function ($entities, $entity, $record) {

            $permissions = [];

            foreach (Utils::getResourcePermissionPrefixes($entity['fqcn']) as $permission) {
                $permissions[$permission.'_'.$entity['resource']] = FilamentShield::getLocalizedResourcePermissionLabel($permission);
            }

            $key = 'resource_'.$entity['resource'];

            $entities[] = Forms\Components\Card::make()
                ->extraAttributes(['class' => 'border-0 shadow-lg'])
                ->schema([
                    CheckboxList::make($key)
                        ->bulkToggleable()
                        ->label(FilamentShield::getLocalizedResourceLabel($entity['fqcn']))
                        ->helperText(Utils::showModelPath($entity['fqcn']))
                        ->columns(2)
                        ->afterStateHydrated(fn (Closure $set, Closure $get, $record) => static::setPermissionsStates($permissions, $set, $key, $record))
                        ->options($permissions),
                ])
                ->columnSpan(1);

            return $entities;
        }, collect())
            ->toArray();
    }
    /**--------------------------------*
    | Resource Related Logic End     |
    *----------------------------------*/

    protected static function getCustomEntities(): ?Collection
    {
        $resourcePermissions = collect();
        collect(FilamentShield::getResources())->each(function ($entity) use ($resourcePermissions) {
            collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->map(function ($permission) use ($resourcePermissions, $entity) {
                $resourcePermissions->push((string) Str::of($permission.'_'.$entity['resource']));
            });
        });

        $entitiesPermissions = $resourcePermissions
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
                        ->afterStateHydrated(function (Closure $set, Closure $get, $record) use ($customPermission) {
                            if (is_null($record)) {
                                return;
                            }

                            $set($customPermission, $record->checkPermissionTo($customPermission));

                        })
                        ->reactive()
                        ->afterStateUpdated(function (Closure $set, Closure $get, $state) {
                            if (! $state) {
                                $set('select_all', false);
                            }

                        })
                        ->dehydrated(fn ($state): bool => $state),
                ])
                ->columns(1)
                ->columnSpan(1);

            return $customEntities;
        }, []);
    }
}
