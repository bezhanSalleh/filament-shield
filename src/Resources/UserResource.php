<?php

namespace BezhanSalleh\FilamentShield\Resources;

use App\Models\User;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\UserResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class UserResource extends Resource
{
    /**
     * Note: this is pre-driver PR.
     * Once the PR is merged, this will require the necessary adjustments.
     */
    protected static ?string $label = 'User';

    protected static ?string $navigationGroup = 'Filament Shield';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('User_Management')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->reactive(),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->unique(User::class, 'email', fn ($record) => $record),
                            ])
                            ->reactive(),
                        Forms\Components\Tabs\Tab::make('Roles')
                            ->schema([
                                Forms\Components\CheckboxList::make('roles')
                                    ->columnSpan('full')
                                    ->reactive()
                                    ->relationship('roles', 'name', function (Builder $query) {
                                        if (! auth()->user()->hasRole('super_admin')) {
                                            return $query->where('name', '<>', 'super_admin');
                                        }

                                        return $query;
                                    })
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        return Str::of($record->name)->headline();
                                    })
                                    ->columns(4),
                            ])
                            ->reactive(),
                        Forms\Components\Tabs\Tab::make('Direct Permissions')
                            ->schema([
                                Forms\Components\Toggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText('Enable all permissions currently <span class="text-primary font-medium">Selected</span> for this user')
                                    ->reactive()
                                    ->afterStateUpdated(function (Closure $set, $state) {
                                        RoleResource::refreshEntitiesStatesViaSelectAll($set, $state);
                                    })
                                    ->dehydrated(fn ($state): bool => $state),
                                Forms\Components\Tabs::make('Permissions')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                                            ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
                                            ->reactive()
                                            ->schema(RoleResource::getResourceEntitiesSchema(static::class)),
                                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.pages'))
                                            ->visible(fn (): bool => (bool) Utils::isPageEntityEnabled() && (count(FilamentShield::getPages()) > 0 ? true : false))
                                            ->reactive()
                                            ->schema(RoleResource::getPageEntityPermissionsSchema()),
                                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                                            ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && (count(FilamentShield::getWidgets()) > 0 ? true : false))
                                            ->reactive()
                                            ->schema(RoleResource::getWidgetEntityPermissionSchema()),
                                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                                            ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled())
                                            ->reactive()
                                            ->schema(RoleResource::getCustomEntitiesPermisssionSchema()),
                                    ])
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 3,
                                    ])
                                    ->columnSpan('full'),
                            ])
                            ->reactive(),
                        Forms\Components\Tabs\Tab::make('Reset Password')
                            ->schema([
                                Forms\Components\Toggle::make('reset_password')
                                    ->columnSpan('full')
                                    ->reactive()
                                    ->dehydrated(false)
                                    ->hiddenOn('create'),
                                Forms\Components\TextInput::make('password')
                                    ->columnSpan('full')
                                    ->visible(fn (string $context, Closure $get) => $context === 'create' || $get('reset_password') == true)
                                    ->rules('max:8')
                                    ->password()
                                    ->required()
                                    ->dehydrateStateUsing(function ($state) {
                                        return \Illuminate\Support\Facades\Hash::make($state);
                                    }),
                            ])
                            ->reactive(),
                    ])
                    ->columnSpanFull(),
                // Forms\Components\Section::make('Details')
                //     ->schema([
                //         Forms\Components\TextInput::make('name')
                //             ->required()
                //             ->reactive(),
                //         Forms\Components\TextInput::make('email')
                //             ->required()
                //             ->email()
                //             ->unique(User::class, 'email', fn ($record) => $record),
                //         Forms\Components\Toggle::make('reset_password')
                //             ->columnSpan('full')
                //             ->reactive()
                //             ->dehydrated(false)
                //             ->hiddenOn('create'),
                //         Forms\Components\TextInput::make('password')
                //             ->columnSpan('full')
                //             ->visible(fn (string $context, Closure $get) => $context === 'create' || $get('reset_password') == true)
                //             ->rules('max:8')
                //             ->password()
                //             ->required()
                //             ->dehydrateStateUsing(function ($state) {
                //                 return \Illuminate\Support\Facades\Hash::make($state);
                //             }),
                //         Forms\Components\CheckboxList::make('roles')
                //             ->columnSpan('full')
                //             ->reactive()
                //             ->relationship('roles', 'name', function (Builder $query) {
                //                 if (! auth()->user()->hasRole('super_admin')) {
                //                     return $query->where('name', '<>', 'super_admin');
                //                 }

                //                 return $query;
                //             })
                //             ->getOptionLabelFromRecordUsing(function ($record) {
                //                 return Str::of($record->name)->headline();
                //             })
                //             ->columns(4),
                //     ])->columns(['md' => 2]),
                // Forms\Components\Section::make('Direct Permissions')
                //     ->description('Instead of roles, you can opt to control a users access more granularily via direct permissions')
                //     ->collapsible()
                //     ->collapsed()
                //     ->compact()
                //     ->schema([
                //         Forms\Components\Toggle::make('select_all')
                //             ->onIcon('heroicon-s-shield-check')
                //             ->offIcon('heroicon-s-shield-exclamation')
                //             ->label(__('filament-shield::filament-shield.field.select_all.name'))
                //             ->helperText('Enable all permissions currently <span class="text-primary font-medium">Selected</span> for this user')
                //             ->reactive()
                //             ->afterStateUpdated(function (Closure $set, $state) {
                //                 RoleResource::refreshEntitiesStatesViaSelectAll($set, $state);
                //             })
                //             ->dehydrated(fn ($state): bool => $state),
                //         Forms\Components\Tabs::make('Permissions')
                //             ->tabs([
                //                 Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                //                     ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
                //                     ->reactive()
                //                     ->schema(RoleResource::getResourceEntitiesSchema(static::class)),
                //                 Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.pages'))
                //                     ->visible(fn (): bool => (bool) Utils::isPageEntityEnabled() && (count(FilamentShield::getPages()) > 0 ? true : false))
                //                     ->reactive()
                //                     ->schema(RoleResource::getPageEntityPermissionsSchema()),
                //                 Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                //                     ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && (count(FilamentShield::getWidgets()) > 0 ? true : false))
                //                     ->reactive()
                //                     ->schema(RoleResource::getWidgetEntityPermissionSchema()),
                //                 Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                //                     ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled())
                //                     ->reactive()
                //                     ->schema(RoleResource::getCustomEntitiesPermisssionSchema()),
                //             ])
                //             ->columns([
                //                 'sm' => 2,
                //                 'lg' => 3,
                //             ])
                //             ->columnSpan('full'),
                //     ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('roles.name')
                    ->formatStateUsing(function ($state) {
                        return Str::of($state)->headline();
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')->relationship('roles', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function getModel(): string
    {
        return Utils::getAuthProviderFQCN();
    }

    protected static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
