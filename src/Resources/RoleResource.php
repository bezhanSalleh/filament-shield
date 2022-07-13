<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasPermissions;
use BezhanSalleh\FilamentShield\Traits\HasDefaultPermissions;
use BezhanSalleh\FilamentShield\Traits\HasPermissionsSchemaForm;
use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

class RoleResource extends Resource implements HasPermissions
{
    use HasDefaultPermissions;
    use HasPermissionsSchemaForm;

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
                                    ->afterStateUpdated(fn(Closure $set, $state): string => $set('name', Str::lower($state))),
                                Forms\Components\TextInput::make('guard_name')
                                    ->label(__('filament-shield::filament-shield.field.guard_name'))
                                    ->default(config('filament.auth.guard'))
                                    ->nullable()
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn(Closure $set, $state): string => $set('guard_name', Str::lower($state))),
                                Forms\Components\Toggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(__('filament-shield::filament-shield.field.select_all.message'))
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        static::refreshEntitiesStatesViaSelectAll($set, $state);
                                    })
                                    ->dehydrated(fn($state):bool => $state)
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3
                            ]),
                    ]),
                Forms\Components\Section::make(__('filament-shield::filament-shield.section'))
                    ->schema([
                        Forms\Components\Tabs::make('Permissions')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                                    ->visible(fn(): bool => (bool) config('filament-shield.entities.resources'))
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 2,
                                            'lg' => 3,
                                        ])
                                        ->schema(static::getResourceEntitiesSchema())
                                        ->columns([
                                            'sm' => 2,
                                            'lg' => 3
                                        ])
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.pages'))
                                    ->visible(fn (): bool => (bool) (config('filament-shield.entities.pages') && count(static::getPageEntities())) > 0 ? true: false)
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getPageEntityPermissionsSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4
                                        ])
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                                    ->visible(fn(): bool => (bool) (config('filament-shield.entities.widgets') && count(static::getWidgetEntities())) > 0 ? true: false)
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getWidgetEntityPermissionSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4
                                        ])
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                                    ->visible(fn(): bool => (bool) config('filament-shield.entities.custom_permissions'))
                                    ->reactive()
                                    ->schema([
                                        Forms\Components\Grid::make([
                                            'sm' => 3,
                                            'lg' => 4,
                                        ])
                                        ->schema(static::getCustomEntitiesPermissionSchema())
                                        ->columns([
                                            'sm' => 3,
                                            'lg' => 4
                                        ])
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
                    ->formatStateUsing(fn($state): string => Str::headline($state))
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
            'settings' => Pages\ShieldSettings::route('/settings'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    public static function getLabel(): string
    {
        return __('filament-shield::filament-shield.resource.label.role');
    }

    public static function getPluralLabel(): string
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
}
