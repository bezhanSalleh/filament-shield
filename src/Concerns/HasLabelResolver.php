<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasLabelResolver
{
    /**
     * Get resource label from Filament's getModelLabel().
     */
    public function getLocalizedResourceLabel(Resource | string $resource): string
    {
        $resource = is_string($resource) ? resolve($resource) : $resource;

        return Str::of($resource::getModelLabel())->headline()->toString();
    }

    /**
     * Get page label using Filament's methods with fallback chain.
     */
    public function getLocalizedPageLabel(Page | string $page): string
    {
        $page = is_string($page) ? resolve($page) : $page;

        return $page->getTitle() // @phpstan-ignore-line
            ?? $page->getHeading() // @phpstan-ignore-line
            ?? $page->getNavigationLabel() // @phpstan-ignore-line
            ?? Str::of(class_basename($page))->headline()->toString();
    }

    /**
     * Get widget label using Filament's methods with fallback chain.
     */
    public function getLocalizedWidgetLabel(Widget | string $widget): string
    {
        $widget = is_string($widget) ? resolve($widget) : $widget;

        return match (true) {
            $widget instanceof TableWidget => (string) invade($widget)->makeTable()->getHeading(), // @phpstan-ignore-line
            $this->hasValidHeading($widget) => (string) invade($widget)->getHeading(),
            default => Str::of(class_basename($widget))->headline()->toString(),
        };
    }

    /**
     * TODO: Just not to get confused later, document steps and remove later if unnecessary.
     * Get localized label for a permission key.
     *
     * Used for: resource affixes, page/widget permissions, custom permissions.
     *
     * Fallback chain:
     * 1. User's translation file (if localization.enabled)
     * 2. Package's resource_permission_prefixes_labels (for affixes only)
     * 3. Provided fallback or headline
     */
    public function getLocalizedLabel(string $key, ?string $fallback = null): string
    {
        $config = Utils::getConfig()->localization;
        $localizationKey = Utils::toLocalizationKey($key);

        // 1. User's translation (if localization enabled)
        if ($config->enabled) {
            $userKey = sprintf('%s.%s', $config->key, $localizationKey);
            if (Lang::has($userKey)) {
                return __($userKey);
            }
        }

        // 2. Package's default translations for affixes
        $packageKey = 'filament-shield::filament-shield.resource_permission_prefixes_labels.' . $localizationKey;
        if (Lang::has($packageKey)) {
            return __($packageKey);
        }

        // 3. Fallback
        return $fallback ?? Str::of($key)->headline()->toString();
    }

    /**
     * Get label for a resource permission affix (view, create, update, etc.).
     */
    public function getAffixLabel(string $affix): string
    {
        return $this->getLocalizedLabel($affix);
    }

    /**
     * Get all affix labels for a resource's policy methods.
     */
    public function getResourceAffixLabels(?string $resource = null): array
    {
        return collect($this->getDefaultPolicyMethodsOrFor($resource))
            ->mapWithKeys(fn (string $method): array => [
                $method => $this->getAffixLabel($method),
            ])
            ->toArray();
    }

    /**
     * Get label for a page/widget permission.
     *
     * When localization is enabled, checks user's translation file.
     * Otherwise, uses Filament's entity methods.
     */
    public function getEntityPermissionLabel(string $entity, string $permissionKey): string
    {
        $config = Utils::getConfig()->localization;

        // If localization enabled, try user's translation first
        if ($config->enabled) {
            $localizationKey = Utils::toLocalizationKey($permissionKey);
            $userKey = sprintf('%s.%s', $config->key, $localizationKey);
            if (Lang::has($userKey)) {
                return __($userKey);
            }
        }

        // Fall back to Filament's entity label methods
        return $this->resolveEntityLabel($entity);
    }

    /**
     * Get label for a custom permission.
     *
     * When localization is enabled, checks user's translation file.
     * Otherwise, uses the provided label or headlines the key.
     */
    public function getCustomPermissionLabel(string $key, ?string $configLabel = null): string
    {
        $config = Utils::getConfig()->localization;
        $localizationKey = Utils::toLocalizationKey($key);

        // If localization enabled, try user's translation first
        if ($config->enabled) {
            $userKey = sprintf('%s.%s', $config->key, $localizationKey);
            if (Lang::has($userKey)) {
                return __($userKey);
            }
        }

        // Fall back to config label or headline
        return $configLabel
            ? Str::of($configLabel)->headline()->toString()
            : Str::of($key)->headline()->toString();
    }

    /**
     * Resolve label for a Filament entity (Resource, Page, or Widget).
     */
    protected function resolveEntityLabel(string $entity): string
    {
        $instance = resolve($entity);

        return match (true) {
            $instance instanceof Resource => $this->getLocalizedResourceLabel($instance),
            $instance instanceof Page => $this->getLocalizedPageLabel($instance),
            $instance instanceof Widget => $this->getLocalizedWidgetLabel($instance),
            default => throw new InvalidArgumentException('Entity must be an instance of Resource, Page, or Widget.'),
        };
    }

    private function hasValidHeading(Widget $widget): bool
    {
        return method_exists($widget, 'getHeading')
            && filled(invade($widget)->getHeading());
    }
}
