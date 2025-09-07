<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Component as Livewire;

trait HasShieldFormComponents
{
    public static function getShieldFormComponents(): Component
    {
        return Tabs::make('Permissions')
            ->contained()
            ->tabs([
                static::getTabFormComponentForResources(),
                static::getTabFormComponentForPage(),
                static::getTabFormComponentForWidget(),
                static::getTabFormComponentForCustomPermissions(),
            ])
            ->columnSpan('full');
    }

    public static function getResourceEntitiesSchema(): ?array
    {
        return collect(FilamentShield::getResources())
            ->map(function (array $entity): \Filament\Schemas\Components\Section {
                $sectionLabel = strval(
                    static::shield()->hasLocalizedPermissionLabels()
                    ? FilamentShield::getLocalizedResourceLabel($entity['resourceFqcn'])
                    : $entity['model']
                );

                return Section::make($sectionLabel)
                    ->description(fn (): \Illuminate\Support\HtmlString => new HtmlString('<span style="word-break: break-word;">' . Utils::showModelPath($entity['modelFqcn']) . '</span>'))
                    ->compact()
                    ->schema([
                        static::getCheckBoxListComponentForResource($entity),
                    ])
                    ->columnSpan(static::shield()->getSectionColumnSpan())
                    ->collapsible();
            })
            ->toArray();
    }

    public static function getResourceTabBadgeCount(): ?int
    {
        return once(
            fn (): int => collect(FilamentShield::getResources())
                ->sum(fn (array $resource): int => count($resource['permissions']))
        );
    }

    public static function getResourcePermissionOptions(array $entity): array
    {
        return once(fn (): array => FilamentShield::getResourcePermissionsWithLabels($entity['resourceFqcn']));
    }

    public static function setPermissionStateForRecordPermissions(Component $component, string $operation, array $permissions, ?Model $record): void
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

    public static function getPageOptions(): array
    {
        return collect(FilamentShield::getPages())
            ->flatMap(fn ($page) => $page['permissions'])
            ->toArray();
    }

    public static function getWidgetOptions(): array
    {
        return collect(FilamentShield::getWidgets())
            ->flatMap(fn ($widget) => $widget['permissions'])
            ->toArray();
    }

    public static function getTabFormComponentForResources(): Component
    {
        return static::shield()->hasSimpleResourcePermissionView()
            ? static::getTabFormComponentForSimpleResourcePermissionsView()
            : Tab::make('resources')
                ->label(__('filament-shield::filament-shield.resources'))
                ->visible(fn (): bool => Utils::isResourceTabEnabled())
                ->badge(static::getResourceTabBadgeCount())
                ->schema([
                    Grid::make()
                        ->schema(static::getResourceEntitiesSchema())
                        ->columns(static::shield()->getGridColumns()),
                ]);
    }

    public static function getCheckBoxListComponentForResource(array $entity): Component
    {
        $permissionsArray = static::getResourcePermissionOptions($entity);

        return static::getCheckboxListFormComponent(
            name: $entity['resourceFqcn'],
            options: $permissionsArray,
            searchable: false,
            columns: static::shield()->getResourceCheckboxListColumns(),
            columnSpan: static::shield()->getResourceCheckboxListColumnSpan()
        );
    }

    public static function getTabFormComponentForPage(): Component
    {
        $options = static::getPageOptions();
        $count = count($options);

        return Tab::make('pages')
            ->label(__('filament-shield::filament-shield.pages'))
            ->visible(fn (): bool => Utils::isPageTabEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent(
                    name: 'pages_tab',
                    options: $options,
                ),
            ]);
    }

    public static function getTabFormComponentForWidget(): Component
    {
        $options = static::getWidgetOptions();
        $count = count($options);

        return Tab::make('widgets')
            ->label(__('filament-shield::filament-shield.widgets'))
            ->visible(fn (): bool => Utils::isWidgetTabEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent(
                    name: 'widgets_tab',
                    options: $options,
                ),
            ]);
    }

    public static function getTabFormComponentForCustomPermissions(): Component
    {
        $options = FilamentShield::getCustomPermissions(static::shield()->hasLocalizedPermissionLabels());
        $count = count($options);

        return Tab::make('custom_permissions')
            ->label(__('filament-shield::filament-shield.custom'))
            ->visible(fn (): bool => Utils::isCustomPermissionTabEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent(
                    name: 'custom_permissions_tab',
                    options: $options,
                ),
            ]);
    }

    public static function getTabFormComponentForSimpleResourcePermissionsView(): Component
    {
        $options = FilamentShield::getAllResourcePermissionsWithLabels();
        $count = once(fn (): int => count($options));

        return Tab::make('resources')
            ->label(__('filament-shield::filament-shield.resources'))
            ->visible(fn (): bool => Utils::isResourceTabEnabled() && $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListFormComponent(
                    name: 'resources_tab',
                    options: $options,
                ),
            ]);
    }

    public static function getCheckboxListFormComponent(string $name, array $options, bool $searchable = true, array | int | string | null $columns = null, array | int | string | null $columnSpan = null): Component
    {
        return CheckboxList::make($name)
            ->hiddenLabel()
            ->options(fn (): array => $options)
            ->searchable($searchable)
            ->live()
            ->afterStateHydrated(function (Component $component, string $operation, ?Model $record, Set $set) use ($options): void {
                static::setPermissionStateForRecordPermissions(
                    component: $component,
                    operation: $operation,
                    permissions: $options,
                    record: $record
                );

                static::toggleSelectAllViaEntities($component->getLivewire(), $set);
            })
            ->afterStateUpdated(function (Livewire $livewire, Set $set): void {
                static::toggleSelectAllViaEntities($livewire, $set);
            })
            ->selectAllAction(fn (
                Action $action,
                Component $component,
                Livewire $livewire,
                Set $set
            ) => static::bulkToggleableAction(
                action: $action,
                component: $component,
                livewire: $livewire,
                set: $set
            ))
            ->deselectAllAction(fn (
                Action $action,
                Component $component,
                Livewire $livewire,
                Set $set
            ) => static::bulkToggleableAction(
                action: $action,
                component: $component,
                livewire: $livewire,
                set: $set,
                resetState: true
            ))
            ->dehydrated(fn ($state): bool => ! blank($state))
            ->bulkToggleable()
            ->gridDirection('row')
            ->columns($columns ?? static::shield()->getCheckboxListColumns())
            ->columnSpan($columnSpan ?? static::shield()->getCheckboxListColumnSpan());
    }

    public static function shield(): FilamentShieldPlugin
    {
        return FilamentShieldPlugin::get();
    }

    public static function getSelectAllFormComponent(): Component
    {
        return Toggle::make('select_all')
            ->onIcon('heroicon-s-shield-check')
            ->offIcon('heroicon-s-shield-exclamation')
            ->label(__('filament-shield::filament-shield.field.select_all.name'))
            ->helperText(fn (): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
            ->live()
            ->afterStateUpdated(function (Livewire $livewire, Set $set, bool $state): void {
                static::toggleEntitiesViaSelectAll($livewire, $set, $state);
            })
            ->dehydrated(fn (bool $state): bool => $state);
    }

    public static function toggleSelectAllViaEntities(Livewire $livewire, Set $set): void
    {
        /** @phpstan-ignore-next-line */
        $entitiesStates = collect($livewire->form->getFlatComponents())
            ->reduce(function (mixed $counts, Component $component) {
                if ($component instanceof CheckboxList) {
                    //  $component->callAfterStateHydrated();
                    $counts[$component->getName()] = count(array_keys($component->getOptions())) === count(collect($component->getState())->values()->unique()->toArray());
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

    public static function toggleEntitiesViaSelectAll(Livewire $livewire, Set $set, bool $state): void
    {
        /** @phpstan-ignore-next-line */
        $entitiesComponents = collect($livewire->form->getFlatComponents())
            ->filter(fn (Component $component): bool => $component instanceof CheckboxList);

        if ($state) {
            $entitiesComponents
                ->each(
                    function (CheckboxList $component) use ($set): void {
                        $set($component->getName(), array_keys($component->getOptions()));
                    }
                );
        } else {
            $entitiesComponents
                ->each(fn (CheckboxList $component): \Filament\Forms\Components\CheckboxList => $component->state([]));
        }
    }

    public static function bulkToggleableAction(Action $action, Component $component, Livewire $livewire, Set $set, bool $resetState = false): void
    {
        $action
            ->livewireClickHandlerEnabled(true)
            ->action(function () use ($component, $livewire, $set, $resetState): void {
                /** @phpstan-ignore-next-line */
                $component->state($resetState ? [] : array_keys($component->getOptions()));
                static::toggleSelectAllViaEntities($livewire, $set);
            });
    }
}
