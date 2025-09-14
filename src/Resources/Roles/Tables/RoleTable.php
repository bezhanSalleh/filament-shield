<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Resources\Roles\Tables;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

/**
 * Default table configuration for Role resources.
 * 
 * This class provides the basic table structure for role management.
 * Users can extend this class or create their own table class to customize
 * the table behavior according to their needs.
 * 
 * @see https://filamentphp.com/docs/4.x/resources/code-quality-tips#using-schema-and-table-classes
 */
class RoleTable
{
    /**
     * Configure the table with default role management functionality.
     * 
     * This method provides basic table configuration including:
     * - Standard columns (name, guard_name, team, permissions_count, updated_at)
     * - Basic record actions (edit, delete)
     * - Basic bulk actions (delete)
     * 
     * @param Table $table The table instance to configure
     * @return Table The configured table
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(static::getColumns())
            ->filters(static::getFilters())
            ->recordActions(static::getRecordActions())
            ->toolbarActions(static::getToolbarActions());
    }

    /**
     * Get the default table columns.
     * 
     * @return array<\Filament\Tables\Columns\Column>
     */
    protected static function getColumns(): array
    {
        return [
            TextColumn::make('name')
                ->weight(FontWeight::Medium)
                ->label(__('filament-shield::filament-shield.column.name'))
                ->formatStateUsing(fn (string $state): string => Str::headline($state))
                ->searchable(),
                
            TextColumn::make('guard_name')
                ->badge()
                ->color('warning')
                ->label(__('filament-shield::filament-shield.column.guard_name')),
                
            TextColumn::make('team.name')
                ->default('Global')
                ->badge()
                ->color(fn (mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                ->label(__('filament-shield::filament-shield.column.team'))
                ->searchable()
                ->visible(fn (): bool => Utils::isTenancyEnabled()),
                
            TextColumn::make('permissions_count')
                ->badge()
                ->label(__('filament-shield::filament-shield.column.permissions'))
                ->counts('permissions')
                ->color('primary'),
                
            TextColumn::make('updated_at')
                ->label(__('filament-shield::filament-shield.column.updated_at'))
                ->dateTime(),
        ];
    }

    /**
     * Get the default table filters.
     * 
     * @return array<\Filament\Tables\Filters\BaseFilter>
     */
    protected static function getFilters(): array
    {
        return [
            // Default implementation has no filters
            // Users can override this method to add custom filters
        ];
    }

    /**
     * Get the default record actions.
     * 
     * @return array<\Filament\Actions\Action>
     */
    protected static function getRecordActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    /**
     * Get the default toolbar actions.
     * 
     * @return array<\Filament\Actions\Action>
     */
    protected static function getToolbarActions(): array
    {
        return [
            DeleteBulkAction::make(),
        ];
    }
}
