<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
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
                                    ->afterStateUpdated(function ($livewire, Forms\Set $set, $state) {
                                        static::experimentalToggleEntitiesViaSelectAll($livewire, $set, $state);
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
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.resources'))
                            ->visible(fn (): bool => (bool) Utils::isResourceEntityEnabled())
                            // ->live()
                            ->badge(static::getResourceTabBadge())
                            ->schema([
                                Forms\Components\Grid::make()
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
                                Forms\Components\CheckboxList::make('pages')
                                    ->label('')
                                    ->options(fn (): array => static::experimentalGetPagePermissions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, Model $record, Forms\Set $set) {
                                        static::experimentalSetPagesStateWhenRecordHasPermission($component, $record);
                                    })
                                    ->afterStateUpdated(
                                        fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set)
                                    )
                                    ->dehydrated(fn ($state) => blank($state) ? false : true)
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 4,
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.widgets'))
                            ->visible(fn (): bool => (bool) Utils::isWidgetEntityEnabled() && (count(FilamentShield::getWidgets()) > 0 ? true : false))
                            // ->live()
                            ->schema([
                                Forms\Components\CheckboxList::make('widgets')
                                    ->label('')
                                    ->options(fn (): array => static::experimentalGetWidgetPermissions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, Model $record, Forms\Set $set) {
                                        static::experimentalSetWidgetsStateWhenRecordHasPermission($component, $record);
                                    })
                                    ->afterStateUpdated(
                                        fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set)
                                    )
                                    ->dehydrated(fn ($state) => blank($state) ? false : true)
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 4,
                                    ])
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('filament-shield::filament-shield.custom'))
                            ->visible(fn (): bool => (bool) Utils::isCustomPermissionEntityEnabled() && (count(static::getCustomEntities()) > 0 ? true : false))
                            ->live()
                            ->schema([
                                Forms\Components\CheckboxList::make('custom_permissions')
                                    ->label('')
                                    ->options(fn (): array => static::experimentalGetCustomPermissions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, Model $record, Forms\Set $set) {
                                        static::experimentalSetCustomPermissionsStateWhenRecordHasPermission($component, $record);
                                        static::experimentalToggleSelectAllViaEntities($livewire, $set);
                                    })
                                    ->afterStateUpdated(
                                        fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set)
                                    )
                                    ->dehydrated(fn ($state) => blank($state) ? false : true)
                                    ->bulkToggleable()
                                    ->gridDirection('row')
                                    ->columns([
                                        'sm' => 2,
                                        'lg' => 4,
                                    ])
                                    ->columnSpanFull(),
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
                    Forms\Components\CheckboxList::make($entity['resource'])
                        ->label(FilamentShield::getLocalizedResourceLabel($entity['fqcn']))
                        ->hint(Utils::showModelPath($entity['fqcn']))
                        ->options(fn (): array => static::experimentalGetEntityPermissions($entity))
                        ->live()
                        ->afterStateHydrated(function (Component $component, Model $record) use ($entity) {
                            static::experimentalSetEntityStateWhenRecordHasPermission($component, $record, $entity);
                        })
                        ->afterStateUpdated(
                            fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set)
                        )
                        ->dehydrated(fn ($state) => blank($state) ? false : true)
                        ->bulkToggleable(),
                ])
                ->columnSpan(1);

            return $entities;
        }, collect())
            ?->toArray() ?? [];
    }

    public static function getResourceTabBadge()
    {
        return collect(FilamentShield::getResources())
            ->map(fn ($resource) => count(static::experimentalGetEntityPermissions($resource)))
            ->sum();
    }

    public static function experimentalGetEntityPermissions(array $entity): array
    {
        return collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->flatMap(fn ($permission) => [
                $permission . '_' . $entity['resource'] => FilamentShield::getLocalizedResourcePermissionLabel($permission),
            ])
            ->toArray();
    }

    public static function experimentalSetEntityStateWhenRecordHasPermission(Component $component, Model $record, array $entity)
    {

        if (blank($record)) {
            return;
        }

        $component->state(
            collect(static::experimentalGetEntityPermissions($entity))
                ->reduce(function ($permissions, $value, $key) use($record) {
                    /** @phpstan-ignore-next-line */
                    if ($record->checkPermissionTo($key)) {
                        $permissions[] = $key;
                    }

                    return $permissions;
                }, collect())
                ->toArray()
        );
    }

    public static function experimentalToggleEntitiesViaSelectAll($livewire, $set, bool $state)
    {
        $entitiesComponents = collect($livewire->form->getFlatComponents())
            ->filter(fn (Component $component) => $component instanceof Forms\Components\CheckboxList);

        if ($state) {
            $entitiesComponents
                ->each(
                    function (Forms\Components\CheckboxList $component) use ($set) {
                        $set($component->getName(), array_keys($component->getOptions()));
                    }
                );
        } else {
            $entitiesComponents
                ->each(fn (Forms\Components\CheckboxList $component) => $component->state([]));
        }
    }

    public static function experimentalToggleSelectAllViaEntities($livewire, $set)
    {
        $entitiesStates = collect($livewire->form->getFlatComponents())
            ->reduce(function ($counts, $component) {
                if ($component instanceof Forms\Components\CheckboxList) {
                    $counts[$component->getName()] = count(array_keys($component->getOptions())) == count($component->getState());
                }

                return $counts;
            }, collect())
            ->values();
        if ($entitiesStates->containsStrict(false)) {
            $set('select_all', false);
        } else {
            $set('select_all', true);
        }
    }

    public static function experimentalGetWidgetPermissions(): array
    {
        return collect(FilamentShield::getWidgets())
            ->flatMap(fn ($widgetPermission) => [
                $widgetPermission => FilamentShield::getLocalizedWidgetLabel($widgetPermission),
            ])
            ->toArray();
    }

    public static function experimentalSetWidgetsStateWhenRecordHasPermission(Component $component, Model $record)
    {

        if (blank($record)) {
            return;
        }

        $component->state(
            collect(static::experimentalGetWidgetPermissions())
                /** @phpstan-ignore-next-line */
                ->filter(fn($value, $key) => $record->checkPermissionTo($key))
                ->keys()
                ->toArray()
        );
    }

    public static function experimentalGetPagePermissions(): array
    {
        return collect(FilamentShield::getPages())
            ->flatMap(fn ($pagePermission) => [
                $pagePermission => FilamentShield::getLocalizedPageLabel($pagePermission),
            ])
            ->toArray();
    }

    public static function experimentalSetPagesStateWhenRecordHasPermission(Component $component, Model $record)
    {

        if (blank($record)) {
            return;
        }

        $component->state(
            collect(static::experimentalGetPagePermissions())
                /** @phpstan-ignore-next-line */
                ->filter(fn($value, $key) => $record->checkPermissionTo($key))
                ->keys()
                ->toArray()
        );
    }

    public static function experimentalGetCustomPermissions(): array
    {
        return collect(static::getCustomEntities())
            ->flatMap(fn ($customPermission) => [
                $customPermission => str($customPermission)->headline()->toString(),
            ])
            ->toArray();
    }

    public static function experimentalSetCustomPermissionsStateWhenRecordHasPermission(Component $component, Model $record)
    {
        if (blank($record)) {
            return;
        }

        $component->state(
            collect(static::experimentalGetCustomPermissions())
                /** @phpstan-ignore-next-line */
                ->filter(fn($value, $key) => $record->checkPermissionTo($key))
                ->keys()
                ->toArray()
        );
    }

    protected static function getCustomEntities(): ?Collection
    {
        $resourcePermissions = collect();
        collect(FilamentShield::getResources())->each(function ($entity) use ($resourcePermissions) {
            collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))->map(function ($permission) use ($resourcePermissions, $entity) {
                $resourcePermissions->push((string) Str::of($permission . '_' . $entity['resource']));
            });
        });

        $entitiesPermissions = $resourcePermissions
            ->merge(FilamentShield::getPages())
            ->merge(FilamentShield::getWidgets())
            ->values();

        return static::$permissionsCollection->whereNotIn('name', $entitiesPermissions)->pluck('name');
    }
}
