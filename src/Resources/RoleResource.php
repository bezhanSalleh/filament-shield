<?php

namespace BezhanSalleh\FilamentShield\Resources;

use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
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
                    ->contained()
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
                            ->schema([
                                Forms\Components\CheckboxList::make('pages_tab')
                                    ->label('')
                                    ->options(fn (): array => static::experimentalGetPageOptions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, string $operation, ?Model $record, Forms\Set $set) {
                                        static::experimentalSetPermissionStateForRecordPermissions(
                                            component: $component,
                                            operation: $operation,
                                            permissions: static::experimentalGetPageOptions(),
                                            record: $record
                                        );
                                        static::experimentalToggleSelectAllViaEntities($livewire, $set);
                                    })
                                    ->afterStateUpdated(fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set))
                                    ->selectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set
                                    ))
                                    ->deselectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set,
                                        resetState: true
                                    ))
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
                            ->live()
                            ->schema([
                                Forms\Components\CheckboxList::make('widgets_tab')
                                    ->label('')
                                    ->options(fn (): array => static::experimentalGetWidgetOptions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, string $operation, ?Model $record, Forms\Set $set) {
                                        static::experimentalSetPermissionStateForRecordPermissions(
                                            component: $component,
                                            operation: $operation,
                                            permissions: static::experimentalGetWidgetOptions(),
                                            record: $record
                                        );

                                        static::experimentalToggleSelectAllViaEntities($livewire, $set);
                                    })
                                    ->afterStateUpdated(fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set))
                                    ->selectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set
                                    ))
                                    ->deselectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set,
                                        resetState: true
                                    ))
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
                                    ->options(fn (): array => static::experimentalGetCustomPermissionOptions())
                                    ->searchable()
                                    ->live()
                                    ->afterStateHydrated(function (Component $component, $livewire, string $operation, ?Model $record, Forms\Set $set) {
                                        static::experimentalSetPermissionStateForRecordPermissions(
                                            component: $component,
                                            operation: $operation,
                                            permissions: static::experimentalGetCustomPermissionOptions(),
                                            record: $record
                                        );
                                        static::experimentalToggleSelectAllViaEntities($livewire, $set);
                                    })
                                    ->afterStateUpdated(fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set))
                                    ->selectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set
                                    ))
                                    ->deselectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                                        action: $action,
                                        component: $component,
                                        livewire: $livewire,
                                        set: $set,
                                        resetState: true
                                    ))
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

            $entities[] = Forms\Components\Section::make(FilamentShield::getLocalizedResourceLabel($entity['fqcn']))
                ->description(Utils::showModelPath($entity['fqcn']))
                ->compact()
                ->extraAttributes(['class' => 'border-0 shadow-lg'])
                ->schema([
                    Forms\Components\CheckboxList::make($entity['resource'])
                        ->label('')
                        // ->hint(Utils::showModelPath($entity['fqcn']))
                        ->options(fn (): array => static::experimentalGetResourcePermissionOptions($entity))
                        ->live()
                        ->afterStateHydrated(function (Component $component, $livewire, string $operation, ?Model $record, Forms\Set $set) use ($entity) {
                            static::experimentalSetPermissionStateForRecordPermissions(
                                component: $component,
                                operation: $operation,
                                permissions: static::experimentalGetResourcePermissionOptions($entity),
                                record: $record
                            );

                            static::experimentalToggleSelectAllViaEntities($livewire, $set);
                        })
                        ->afterStateUpdated(fn ($livewire, Forms\Set $set) => static::experimentalToggleSelectAllViaEntities($livewire, $set))
                        ->selectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                            action: $action,
                            component: $component,
                            livewire: $livewire,
                            set: $set
                        ))
                        ->deselectAllAction(fn (FormAction $action, Component $component, $livewire, Forms\Set $set) => static::bulkToggleableAction(
                            action: $action,
                            component: $component,
                            livewire: $livewire,
                            set: $set,
                            resetState: true
                        ))
                        ->dehydrated(fn ($state) => blank($state) ? false : true)
                        ->bulkToggleable()
                        ->gridDirection('row')
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                        ]),
                ])
                ->columnSpan(1);

            return $entities;
        }, collect())
            ?->toArray() ?? [];
    }

    public static function getResourceTabBadge(): ?int
    {
        return collect(FilamentShield::getResources())
            ->map(fn ($resource) => count(static::experimentalGetResourcePermissionOptions($resource)))
            ->sum();
    }

    public static function experimentalGetResourcePermissionOptions(array $entity): array
    {
        return collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->flatMap(fn ($permission) => [
                $permission . '_' . $entity['resource'] => FilamentShield::getLocalizedResourcePermissionLabel($permission),
            ])
            ->toArray();
    }

    public static function experimentalSetPermissionStateForRecordPermissions(Component $component, string $operation, array $permissions, ?Model $record): void
    {

        if (in_array($operation, ['edit', 'view'])) {

            if (blank($record)) {
                return;
            }
            if ($component->isVisible() && count($permissions) > 0) {
                $component->state(
                    collect($permissions)
                        /** @phpstan-ignore-next-line */
                        ->filter(fn ($value, $key) => $record->checkPermissionTo($key))
                        ->keys()
                        ->toArray()
                );
            }
        }
    }

    public static function experimentalToggleEntitiesViaSelectAll($livewire, Forms\Set $set, bool $state): void
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

    public static function experimentalToggleSelectAllViaEntities($livewire, Forms\Set $set): void
    {
        $entitiesStates = collect($livewire->form->getFlatComponents())
            ->reduce(function ($counts, $component) {
                if ($component instanceof Forms\Components\CheckboxList) {
                    $counts[$component->getName()] = count(array_keys($component->getOptions())) == count(collect($component->getState())->values()->unique()->toArray());
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

    public static function experimentalGetPageOptions(): array
    {
        return collect(FilamentShield::getPages())
            ->flatMap(fn ($pagePermission) => [
                $pagePermission => FilamentShield::getLocalizedPageLabel($pagePermission),
            ])
            ->toArray();
    }

    public static function experimentalGetWidgetOptions(): array
    {
        return collect(FilamentShield::getWidgets())
            ->flatMap(fn ($widgetPermission) => [
                $widgetPermission => FilamentShield::getLocalizedWidgetLabel($widgetPermission),
            ])
            ->toArray();
    }

    public static function experimentalGetCustomPermissionOptions(): array
    {
        return collect(static::getCustomEntities())
            ->flatMap(fn ($customPermission) => [
                $customPermission => str($customPermission)->headline()->toString(),
            ])
            ->toArray();
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

    public static function bulkToggleableAction(FormAction $action, Component $component, $livewire, Forms\Set $set, bool $resetState = false): void
    {
        $action
            ->livewireClickHandlerEnabled(true)
            ->action(function () use($component, $livewire, $set, $resetState) {
                /** @phpstan-ignore-next-line */
                $component->state($resetState ? [] : array_keys($component->getOptions()));
                static::experimentalToggleSelectAllViaEntities($livewire, $set);
            });
    }
}
