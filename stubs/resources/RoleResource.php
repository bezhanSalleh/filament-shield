<?php

namespace App\Filament\Resources\Shield;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Permission;
use App\Filament\Resources\Shield\RoleResource\Pages;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make()
                ->schema([
                    Forms\Components\Card::make()
                        ->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn(Closure $set, $state): string => $set('name', Str::lower($state))),
                            Forms\Components\TextInput::make('guard_name')
                                ->default(config('filament.auth.guard'))
                                ->required()
                                ->maxLength(255)
                                ->afterStateUpdated(fn(Closure $set, $state): string => $set('guard_name', Str::lower($state))),
                            Forms\Components\Toggle::make('select_all')
                                ->onIcon('heroicon-s-shield-check')
                                ->offIcon('heroicon-s-shield-exclamation')
                                ->label('Select All')
                                ->helperText('Enable all Permissions for this role.')
                                ->reactive()
                                ->afterStateUpdated(function (Closure $set, $state) {
                                    foreach (static::getEntities() as $entity) {
                                        $set($entity, $state);
                                        foreach(config('filament-shield.default_permission_prefixes') as $permission)
                                        {
                                            $set($permission.'_'.$entity, $state);
                                        }
                                    }

                                })
                                ->dehydrated(fn($state):bool => $state?:false)
                        ])
                        ->columns([
                            'sm' => 2,
                            'lg' => 3
                        ]),
                ]),
                Forms\Components\Grid::make([
                    'sm' => 2,
                    'lg' => 3,
                ])
                ->schema(static::getEntitySchema())
                ->columns([
                    'sm' => 2,
                    'lg' => 3
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->formatStateUsing(fn($state): string => Str::headline($state)),
                Tables\Columns\TextColumn::make('guard_name')
                    ->formatStateUsing(fn($state): string => Str::headline($state)),
                Tables\Columns\BadgeColumn::make('permissions')
                    ->formatStateUsing(fn($record): int => $record->permissions->count())
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('updated_at')
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
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }

    protected static function getEntities(): ?array
    {
        return Permission::pluck('name')
            ->reduce(function ($roles, $permission) {
                $roles->push(Str::afterLast($permission, '_'));
                return $roles->unique();
            },collect())
            ->toArray();
    }

    public static function getEntitySchema()
    {
        return collect(static::getEntities())->reduce(function($entities,$entity) {
                $entities[] = Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Toggle::make($entity)
                            ->onIcon('heroicon-s-lock-open')
                            ->offIcon('heroicon-s-lock-closed')
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set,Closure $get, $state) use($entity) {

                                collect(config('filament-shield.default_permission_prefixes'))->each(function ($permission) use($set, $entity, $state) {
                                        $set($permission.'_'.$entity, $state);
                                });

                                if (! $state) {
                                    $set('select_all',false);
                                }

                                static::refreshSelectAllState($set, $get);
                            })
                            ->dehydrated(false)
                            ,
                        Forms\Components\Fieldset::make('Permissions')
                        ->extraAttributes(['class' => 'text-primary-600','style' => 'border-color:var(--primary)'])
                        ->columns([
                            'default' => 2,
                            'xl' => 2
                        ])
                        ->schema(static::getPermissionsSchema($entity))
                    ])
                    ->columns(2)
                    ->columnSpan(1);
                return $entities;
        },[]);
    }

    public static function getPermissionsSchema($entity)
    {
        return collect(config('filament-shield.default_permission_prefixes'))->reduce(function ($permissions, $permission) use ($entity) {
            $permissions[] = Forms\Components\Checkbox::make($permission.'_'.$entity)
                ->label(Str::studly($permission))
                ->extraAttributes(['class' => 'text-primary-600'])
                ->afterStateHydrated(function (Closure $set, Closure $get, $record) use($entity, $permission) {
                    if (is_null($record)) return;

                    $set($permission.'_'.$entity, $record->hasPermissionTo($permission.'_'.$entity));

                    static::refreshEntityStateAfterHydrated($record, $set, $entity);

                    static::refreshSelectAllState($set, $get);
                })
                ->reactive()
                ->afterStateUpdated(function (Closure $set, Closure $get, $state) use($entity){

                    static::refreshEntityStateAfterUpdate($set, $get, Str::of($entity));

                    if(!$state) {
                        $set($entity,false);
                        $set('select_all',false);
                    }

                    static::refreshSelectAllState($set, $get);
                })
                ->dehydrated(fn($state): bool => $state?:false);
            return $permissions;
        },[]);
    }

    protected static function refreshSelectAllState(Closure $set, Closure $get): void
    {
        $entityStates = collect(static::getEntities())
            ->map(function($entity) use($get){
                return (bool) $get($entity);
            });

        if ($entityStates->containsStrict(false) === false) {
            $set('select_all', true);
        }

        if ($entityStates->containsStrict(false) === true) {
            $set('select_all', false);
        }
    }

    protected static function refreshEntityStateAfterUpdate(Closure $set, Closure $get, string $entity): void
    {
        $permissionStates = collect(config('filament-shield.default_permission_prefixes'))
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

    protected static function refreshEntityStateAfterHydrated(Model $record, Closure $set, string $entity): void
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
                if ($role === 6) {
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
}
