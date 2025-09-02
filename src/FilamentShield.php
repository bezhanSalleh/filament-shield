<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Commands\GenerateCommand;
use BezhanSalleh\FilamentShield\Commands\InstallCommand;
use BezhanSalleh\FilamentShield\Commands\PublishCommand;
use BezhanSalleh\FilamentShield\Commands\SetupCommand;
use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FilamentShield
{
    use Concerns\HasConfig;
    use Concerns\HasEntityDiscovery;
    use Concerns\HasEntityTransformers;
    use Concerns\HasLabelResolver;
    use Concerns\HasResourceHelpers;
    use EvaluatesClosures;

    protected ?Closure $buildPermissionKeyUsing = null;

    public function buildPermissionKeyUsing(Closure $callback): static
    {
        $this->buildPermissionKeyUsing = $callback;

        return $this;
    }

    public function getResources(): ?array
    {
        return once(fn (): ?array => $this->transformResources());
    }

    public function getPages(): ?array
    {
        return once(fn (): ?array => $this->transformPages());
    }

    public function getWidgets(): ?array
    {
        return once(fn (): ?array => $this->transformWidgets());
    }

    public function getCustomPermissions(): ?array
    {
        return once(fn (): ?array => $this->transformCustomPermissions());
    }

    /**
     * Get the localized resource permission label
     */
    public static function getLocalizedResourcePermissionLabel(string $permission): string
    {
        return Lang::has("filament-shield::filament-shield.resource_permission_prefixes_labels.$permission", app()->getLocale())
            ? __("filament-shield::filament-shield.resource_permission_prefixes_labels.$permission")
            : Str::of($permission)->headline();
    }

    private function buildPermissionKey(string $entity, string $affix, string $subject): string
    {
        $permissionConfig = $this->getConfig()->permissions;

        if ($this->buildPermissionKeyUsing instanceof \Closure) {

            /** @var string $result */
            $result = $this->evaluate(
                value: $this->buildPermissionKeyUsing,
                namedInjections: [
                    'entity' => $entity,
                    'affix' => $affix,
                    'subject' => $subject,
                    'case' => $permissionConfig->case,
                    'separator' => $permissionConfig->separator,
                ]
            );

            return $result;
        }

        return $this->format($permissionConfig->case, $affix) . $permissionConfig->separator . $this->format($permissionConfig->case, $subject);
    }

    public function getDefaultPermissionKeys(string $entity, string | array $affixes): array
    {
        $subject = $this->resolveSubject($entity);

        if (is_array($affixes)) {
            return collect($affixes)
                ->mapWithKeys(fn (string $affix): array => [
                    $this->format('camel', $affix) => [
                        'key' => $this->buildPermissionKey($entity, $affix, $subject),
                        'label' => $this->getAffixLabel($affix) . ' ' . $this->resolveLabel($entity),
                    ],
                ])
                ->uniqueStrict()
                ->toArray();
        }

        return [$this->buildPermissionKey($entity, $affixes, $subject) => $this->resolveLabel($entity)];
    }

    protected function resolveSubject(string $entity): string
    {
        $entity = resolve($entity);
        $permissionConfig = $this->getConfig()->permissions;

        $subject = match (true) {
            $entity instanceof Resource => $permissionConfig->resource->subject,
            $entity instanceof Page => $permissionConfig->page->subject,
            $entity instanceof Widget => $permissionConfig->widget->subject,
            default => throw new InvalidArgumentException('Entity must be an instance of Resource, Page, or Widget.'),
        };

        if ($subject === 'model' && method_exists($entity::class, 'getModel')) {
            return class_basename($entity::getModel());
        }

        return class_basename($entity);
    }

    protected function format(string $case, string $value): string
    {
        return match ($case) {
            'kebab' => Str::of($value)->kebab()->toString(),
            'pascal' => Str::of($value)->studly()->toString(),
            'upper_snake' => Str::of($value)->snake()->upper()->toString(),
            'lower_snake' => Str::of($value)->snake()->lower()->toString(),
            'camel' => Str::of($value)->camel()->toString(),
            default => Str::of($value)->snake()->toString(),
        };
    }

    public function getAllResourcePermissions(): array
    {
        return collect($this->getResources())
            ->map(fn (array $resourceEntity): array => collect(
                Utils::getResourcePermissionPrefixes($resourceEntity['fqcn'])
            )
                ->flatMap(function (string $permission) use ($resourceEntity): array {
                    $name = $permission . '_' . $resourceEntity['resource'];
                    $permissionLabel = FilamentShieldPlugin::get()->hasLocalizedPermissionLabels()
                        ? str(static::getLocalizedResourcePermissionLabel($permission))
                            ->prepend(
                                str($resourceEntity['fqcn']::getPluralModelLabel())
                                    ->title()
                                    ->append(' - ')
                                    ->toString()
                            )
                            ->toString()
                        : $name;

                    return [
                        $name => $permissionLabel,
                    ];
                })
                ->toArray())
            ->sortKeys()
            ->collapse()
            ->toArray();
    }

    public function getEntitiesPermissions(): ?array
    {
        return collect($this->getAllResourcePermissions())->keys()
            ->merge(collect($this->getPages())->map->permission->keys())
            ->merge(collect($this->getWidgets())->map->permission->keys())
            ->merge(collect($this->getCustomPermissions())->keys())
            ->values()
            ->flatten()
            ->unique()
            ->toArray();
    }
}
