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

    /**
     * Transform custom permissions from config into formatted key => label pairs.
     *
     * When `format_custom_permission_keys` is enabled (default), permission keys are
     * formatted according to the configured case. If the key contains the configured
     * separator, each segment is formatted independently and rejoined.
     *
     * When a `buildPermissionKeyUsing` closure is registered, custom permissions are
     * routed through it with `entity` set to `'custom'` and `affix` set to `null`.
     * Returning `null` from the closure falls back to the default formatting behavior.
     *
     * When `format_custom_permission_keys` is disabled, keys are used exactly as
     * defined in config — useful for externally managed permissions (Terraform, Keycloak, etc.).
     *
     * @return array<string, string>
     */
    public function transformCustomPermissions(bool $localized = false): ?array
    {
        $permissionConfig = Utils::getConfig()->permissions;
        $formatKeys = $permissionConfig->format_custom_permission_keys ?? true;

        return collect(Utils::getConfig()->custom_permissions)
            ->mapWithKeys(function (string $label, int | string $key) use ($localized, $permissionConfig, $formatKeys): array {
                $permission = is_numeric($key) ? $label : $key;
                $configLabel = is_numeric($key) ? null : $label;

                $formattedKey = $this->resolveCustomPermissionKey(
                    $permission,
                    $permissionConfig->case,
                    $permissionConfig->separator,
                    $formatKeys,
                );

                return [
                    $formattedKey => $localized
                        ? $this->getCustomPermissionLabel($permission, $configLabel)
                        : Str::of($label)->headline()->toString(),
                ];
            })
            ->toArray();
    }

    /**
     * Resolve the final permission key for a custom permission.
     *
     * Priority: closure (if registered and returns non-null) > format (if enabled) > raw key.
     */
    protected function resolveCustomPermissionKey(string $permission, string $case, string $separator, bool $formatKeys): string
    {
        // Route through the custom builder closure if registered
        if ($this->buildPermissionKeyUsing instanceof \Closure) {
            $result = $this->evaluate(
                value: $this->buildPermissionKeyUsing,
                namedInjections: [
                    'entity' => 'custom',
                    'affix' => null,
                    'subject' => $permission,
                    'case' => $case,
                    'separator' => $separator,
                ]
            );

            // Non-null return means the closure handled it
            if ($result !== null) {
                return $result;
            }
        }

        // If formatting is disabled, return the key as-is
        if (! $formatKeys) {
            return $permission;
        }

        return $this->formatCustomPermissionKey($permission, $case, $separator);
    }

    /**
     * Format a custom permission key, handling separator-delimited segments independently.
     *
     * If the permission contains the configured separator (e.g. 'view:system_log'),
     * each segment is formatted separately and rejoined with the separator
     * (e.g. 'View:SystemLog' for pascal case).
     */
    protected function formatCustomPermissionKey(string $permission, string $case, string $separator): string
    {
        if (str_contains($permission, $separator)) {
            return collect(explode($separator, $permission))
                ->map(fn (string $segment): string => $this->format($case, $segment))
                ->implode($separator);
        }

        return $this->format($case, $permission);
    }

    protected function getResourcesToManage(): array
    {
        return collect(Utils::getConfig()->resources->manage)->toArray();
    }

    protected function getDefaultPolicyMethodsOrFor(?string $resource = null): array
    {
        $policyConfig = Utils::getConfig()->policies;
        $defaultPolicyMethods = $policyConfig->methods;

        if (filled($resource)) {
            $resourcePolicyMethods = data_get($this->getResourcesToManage(), $resource);

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
