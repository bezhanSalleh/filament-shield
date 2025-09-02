<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \BezhanSalleh\FilamentShield\FilamentShield buildResourcePermissionKeysUsing(\Closure $callback)
 * @method static \BezhanSalleh\FilamentShield\FilamentShield buildNonResourcePermissionKeyUsing(\Closure $callback)
 * @method static array|string getResourcePermissionKeys(string $entity, array $affixes)
 * @method static array|string getNonResourcePermissionKey(string $entity, string $affix)
 * @method static void generateForResource(string $resourceKey)
 * @method static void generateForPage(string $page)
 * @method static void generateForWidget(string $widget)
 * @method static void generateCustomPermissions()
 * @method static \Spatie\Permission\Models\Role createRole(string|null $name = null, string|int|null $tenantId = null)
 * @method static array|null getResources()
 * @method static string getLocalizedResourcePermissionLabel(string $permission)
 * @method static array|null getPages()
 * @method static array|null getWidgets()
 * @method static array getDefaultResourcePermissionKeys(string $resource, array $affixes)
 * @method static array getDefaultNonResourcePermissionKey(string $resource, string $affix)
 * @method static array getAllResourcePermissions()
 * @method static string getLocalizedOrFormattedCustomPermissionLabel(string $permission)
 * @method static array getCustomPermissions(bool $localizedOrFormatted = false)
 * @method static array|null getEntitiesPermissions()
 * @method static array|null getResourcePermissions(string $key)
 * @method static array|null getResourcePolicyActions(string $key)
 * @method static array|null getResourcePolicyActionsWithPermissions(string $key)
 * @method static array getLocalizedResourceAffixes()
 * @method static string getAffixLabel(string $affix)
 * @method static string getLocalizedResourceLabel(\Filament\Resources\Resource $resource)
 * @method static string getLocalizedPageLabel(\Filament\Pages\Page $page)
 * @method static string getLocalizedWidgetLabel(\Filament\Widgets\Widget $widget)
 * @method static string getGeneratorOption()
 * @method static void prohibitDestructiveCommands(bool $prohibit = true)
 * @method static mixed evaluate(mixed $value, array $namedInjections = [], array $typedInjections = [])
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
