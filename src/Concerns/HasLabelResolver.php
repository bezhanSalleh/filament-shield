<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Widgets\TableWidget;
use Filament\Widgets\Widget;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

trait HasLabelResolver
{
    protected function resolveLabel(string $entity): string
    {
        $entity = resolve($entity);

        return match (true) {
            $entity instanceof Resource => $this->getLocalizedResourceLabel($entity),
            $entity instanceof Page => $this->getLocalizedPageLabel($entity),
            $entity instanceof Widget => $this->getLocalizedWidgetLabel($entity),
            default => throw new InvalidArgumentException('Entity must be an instance of Resource, Page, or Widget.'),
        };
    }

    public function getLocalizedResourceLabel(Resource | string $resource): string
    {
        $resource = is_string($resource) ? resolve($resource) : $resource;

        return Str::of($resource::getModelLabel())->headline()->toString();
    }

    public function getLocalizedPageLabel(Page | string $page): string
    {
        $page = is_string($page) ? resolve($page) : $page;

        return $page->getTitle() // @phpstan-ignore-line
                ?? $page->getHeading() // @phpstan-ignore-line
                ?? $page->getNavigationLabel() // @phpstan-ignore-line
                ?? __(Str::of(class_basename($page))
                    ->snake()
                    ->prepend(Utils::getConfig()->localization->key . '.')
                    ->toString())
                ?? Str::of(class_basename($page))->headline()->toString();
    }

    public function getLocalizedWidgetLabel(Widget | string $widget): string
    {
        $widget = is_string($widget) ? resolve($widget) : $widget;

        return match (true) {
            $widget instanceof TableWidget => (string) invade($widget)->makeTable()->getHeading(), // @phpstan-ignore-line
            $this->hasValidHeading($widget) => (string) invade($widget)->getHeading(),
            default => __(Str::of(class_basename($widget))->snake()->prepend(Utils::getConfig()->localization->key . '.')->toString()) ?? str($widget)
                ->afterLast('\\')
                ->headline()
                ->toString(),
        };
    }

    private function hasValidHeading(Widget $widgetInstance): bool
    {
        return $widgetInstance instanceof Widget // @phpstan-ignore-line
            && method_exists($widgetInstance, 'getHeading')
            && filled(invade($widgetInstance)->getHeading());
    }

    public function getAffixLabel(string $affix, ?string $resource = null): string
    {
        return Arr::get(
            array: $this->getLocalizedResourceAffixes($resource),
            key: Str::of($affix)->camel()->toString(),
            default: Str::of($affix)->headline()->toString()
        );
    }

    public function getLocalizedResourceAffixes(?string $resource = null): array
    {
        return collect($this->getDefaultPolicyMethodsOrFor($resource))
            ->mapWithKeys(fn ($method): array => [
                $method => $this->getPermissionLabel(Str::of($method)->snake()->toString()),
            ])
            ->toArray();
    }

    public function getPermissionLabel(string $permission): string
    {
        $localizationConfig = Utils::getConfig()->localization;

        return $localizationConfig->enabled && Lang::has("$localizationConfig->key.$permission")
            ? __("$localizationConfig->key.$permission")
            : Str::of($permission)->headline()->toString();
    }
}
