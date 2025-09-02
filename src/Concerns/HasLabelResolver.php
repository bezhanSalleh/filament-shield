<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use Filament\Pages\Page;
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

    public function getLocalizedResourceLabel(Resource $resource): string
    {
        return Str::of($resource::getModelLabel())->headline()->toString();
    }

    public function getLocalizedPageLabel(Page $page): string
    {
        return $page->getTitle()
                ?? $page->getHeading()
                ?? $page->getNavigationLabel()
                ?? __(Str::of(class_basename($page))
                    ->snake()
                    ->prepend($this->getConfig()->permissions->localization->key . '.')
                    ->toString())
                ?? Str::of(class_basename($page))->headline()->toString();
    }

    public function getLocalizedWidgetLabel(Widget $widget): string
    {
        return match (true) {
            $widget instanceof TableWidget => (string) invade($widget)->makeTable()->getHeading(), // @phpstan-ignore-line
            $this->hasValidHeading($widget) => (string) invade($widget)->getHeading(),
            default => __(Str::of(class_basename($widget))->snake()->prepend($this->getConfig()->permissions->localization->key . '.')->toString()) ?? str($widget)
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

    public function getAffixLabel(string $affix): string
    {
        return Arr::get(
            array: $this->getLocalizedResourceAffixes(),
            key: Str::of($affix)->camel()->toString(),
            default: Str::of($affix)->headline()->toString()
        );
    }

    public function getLocalizedResourceAffixes(): array
    {
        $config = $this->getConfig();

        return collect($config->policies->methods)
            ->mapWithKeys(fn ($method): array => [
                $method => $this->getPermissionLabel(Str::of($method)->snake()->toString()),
            ])
            ->toArray();
    }

    public function getPermissionLabel(string $permission): string
    {
        $localizationConfig = $this->getConfig()->permissions->localization;

        return $localizationConfig->enabled && Lang::has("$localizationConfig->key.$permission")
            ? __("$localizationConfig->key.$permission")
            : Str::of($permission)->headline()->toString();
    }
}
