<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Str;

trait HasEntityTransformers
{
    public function transformResources(): ?array
    {
        return $this->discoverResources()
            ->reject(fn (string $resource): bool => in_array($resource, Utils::getConfig()->resources->exclude))
            ->mapWithKeys(fn (string $resource): array => [
                $resource => [
                    'resourceFqcn' => $resource,
                    'model' => class_basename($resource::getModel()),
                    'modelFqcn' => str($resource::getModel())->toString(),
                    'permissions' => $this->getDefaultPermissionKeys($resource, $this->getDefaultPolicyMethodsOrFor($resource)),
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

                return in_array($page, Utils::getConfig()->pages->exclude);
            })
            ->mapWithKeys(fn (string $page): array => [
                $page => [
                    'pageFqcn' => $page,
                    'permissions' => $this->getDefaultPermissionKeys($page, Utils::getConfig()->pages->prefix),
                ],
            ])
            ->toArray();
    }

    public function transformWidgets(): ?array
    {
        return $this->discoverWidgets()
            ->reject(fn (string | WidgetConfiguration $widget): bool => in_array(
                needle: $this->getWidgetInstanceFromWidgetConfiguration($widget),
                haystack: Utils::getConfig()->widgets->exclude
            ))
            ->mapWithKeys(fn (string | WidgetConfiguration $widget): array => [
                $widget => [
                    'widgetFqcn' => $this->getWidgetInstanceFromWidgetConfiguration($widget),
                    'permissions' => $this->getDefaultPermissionKeys($widget, Utils::getConfig()->widgets->prefix),
                ],
            ])
            ->toArray();
    }

    /** @return array<string, string> */
    public function transformCustomPermissions(bool $localizedOrFormatted = false): ?array
    {
        return collect(Utils::getConfig()->custom_permissions)
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

    protected function getResourcesToManage(): array
    {
        return collect(Utils::getConfig()->resources->manage)
            ->mapWithKeys(fn (array $methods, string $key) => [basename($key) => $methods])
            ->toArray();
    }

    protected function getDefaultPolicyMethodsOrFor(?string $resource = null): array
    {
        $policyConfig = Utils::getConfig()->policies;
        $defaultPolicyMethods = $policyConfig->methods;

        if (filled($resource)) {
            $resourcePolicyMethods = data_get($this->getResourcesToManage(), basename($resource), null);

            $defaultPolicyMethods = $policyConfig->merge
                ? array_merge($defaultPolicyMethods, $resourcePolicyMethods ?? [])
                : $resourcePolicyMethods ?? $defaultPolicyMethods;
        }

        return collect($defaultPolicyMethods)
            ->map(fn ($method): string => $this->format('camel', $method))
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
