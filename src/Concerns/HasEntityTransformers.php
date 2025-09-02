<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Str;

trait HasEntityTransformers
{
    public function transformResources(): ?array
    {
        return $this->discoverResources()
            ->reject(fn (string $resource): bool => in_array($resource, $this->getConfig()->exclude->resources))
            ->mapWithKeys(fn (string $resource): array => [
                $resource => [
                    'resourceFqcn' => $resource,
                    'model' => class_basename($resource::getModel()),
                    'modelFqcn' => str($resource::getModel())->toString(),
                    'permissions' => $this->getDefaultPermissionKeys($resource, $this->getAffixesFor($resource)),
                ],
            ])
            ->sortKeys()
            ->toArray();
    }

    public function transformPages(): ?array
    {
        $clusters = $this->discoverPages()
            ->map(fn (string $page): ?string => $page::getCluster())
            ->reject(fn (mixed $cluster): bool => is_null($cluster))
            ->unique()
            ->values()
            ->toArray();

        return $this->discoverPages()
            ->reject(function (string $page) use ($clusters): bool {
                if (in_array($page, $clusters)) {
                    return true;
                }

                return in_array($page, $this->getConfig()->exclude->pages);
            })
            ->mapWithKeys(fn (string $page): array => [
                $page => [
                    'pageFqcn' => $page,
                    'permissions' => $this->getDefaultPermissionKeys($page, $this->getConfig()->permissions->page->prefix),
                ],
            ])
            ->toArray();
    }

    public function transformWidgets(): ?array
    {
        return $this->discoverWidgets()
            ->reject(fn (string | WidgetConfiguration $widget): bool => in_array(
                needle: $this->getWidgetInstanceFromWidgetConfiguration($widget),
                haystack: $this->getConfig()->exclude->widgets
            ))
            ->mapWithKeys(fn (string | WidgetConfiguration $widget): array => [
                $widget => [
                    'widgetFqcn' => $this->getWidgetInstanceFromWidgetConfiguration($widget),
                    'permissions' => $this->getDefaultPermissionKeys($widget, $this->getConfig()->permissions->widget->prefix),
                ],
            ])
            ->toArray();
    }

    /** @return array<string, string> */
    public function transformCustomPermissions(bool $localizedOrFormatted = false): ?array
    {
        return collect($this->getConfig()->custom_permissions)
            ->mapWithKeys(function (string $label, int | string $key) use ($localizedOrFormatted): array {
                $permission = is_numeric($key) ? $label : $key;

                return [
                    Str::of($permission)->snake()->toString() => $localizedOrFormatted
                        ? $this->getPermissionLabel($permission)
                        : Str::of($label)->headline()->toString(),
                ];
            })
            ->toArray();
    }

    protected function getAffixesFor(string $resource): array
    {
        $policyConfig = $this->getConfig()->policies;
        $methods = [];

        if (method_exists($resource, 'getPermissionPrefixes')) {
            $methods = $resource::getPermissionPrefixes();
        }

        if ($policyConfig->merge) {
            $methods = array_merge($methods, $policyConfig->methods);
        }

        return collect($methods)
            ->map(fn ($affix): string => $this->format('camel', $affix))
            ->unique()
            ->toArray();
    }

    protected function getWidgetInstanceFromWidgetConfiguration(string | WidgetConfiguration $widget): string
    {
        return $widget instanceof WidgetConfiguration
            ? $widget->widget
            : $widget;
    }
}
