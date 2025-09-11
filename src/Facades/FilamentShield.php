<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \BezhanSalleh\FilamentShield\FilamentShield buildPermissionKeyUsing(\Closure $callback)
 * @method static array|null getResources()
 * @method static array|null getPages()
 * @method static array|null getWidgets()
 * @method static array|null getCustomPermissions(bool $localized = false)
 * @method static string getLocalizedResourcePermissionLabel(string $permission)
 * @method static string defaultPermissionKeyBuilder(string $affix, string $separator, string $subject, string $case)
 * @method static array getDefaultPermissionKeys(string $entity, array|string $affixes)
 * @method static array|null getEntitiesPermissions()
 * @method static void prohibitDestructiveCommands(bool $prohibit = true)
 * @method static \Illuminate\Support\Collection|null discoverEntities(string $entityType)
 * @method static \Illuminate\Support\Collection discoverResources()
 * @method static \Illuminate\Support\Collection discoverPages()
 * @method static \Illuminate\Support\Collection discoverWidgets()
 * @method static array|null transformResources()
 * @method static array|null transformPages()
 * @method static array|null transformWidgets()
 * @method static array transformCustomPermissions(bool $localizedOrFormatted = false)
 * @method static string getLocalizedResourceLabel(\Filament\Resources\Resource|string $resource)
 * @method static string getLocalizedPageLabel(\Filament\Pages\BasePage|string $page)
 * @method static string getLocalizedWidgetLabel(\Filament\Widgets\Widget|string $widget)
 * @method static string getAffixLabel(string $affix, string|null $resource = null)
 * @method static array getLocalizedResourceAffixes(string|null $resource = null)
 * @method static string getPermissionLabel(string $permission)
 * @method static array|null getResourcePermissions(string $key)
 * @method static array|null getResourcePolicyActions(string $key)
 * @method static array|null getResourcePermissionsWithLabels(string $key)
 * @method static array|null getResourcePolicyActionsWithPermissions(string $key)
 * @method static array getAllResourcePermissionsWithLabels()
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
