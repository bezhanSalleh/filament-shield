<?php

namespace BezhanSalleh\FilamentShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string getPermissionIdentifier(string $resource)
 * @method static void generateForResource(array $entity)
 * @method static void generateForPage(string $page)
 * @method static void generateForWidget(string $widget)
 * @method static ?array getResources()
 * @method static ?array getPages()
 * @method static ?array getWidgets()
 * @method static string getLocalizedResourceLabel(string $entity)
 * @method static string getLocalizedResourcePermissionLabel(string $permission)
 * @method static string getLocalizedPageLabel(string $page)
 * @method static string getLocalizedWidgetLabel(string $widget)
 *
 * @see \BezhanSalleh\FilamentShield\FilamentShield
 */
class FilamentShield extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'filament-shield';
    }
}
