<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Widgets\Widget;
use Illuminate\Support\Str;
use InvalidArgumentException;

class FilamentShield
{
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

    public function getCustomPermissions(bool $localized = false): ?array
    {
        return once(fn (): ?array => $this->transformCustomPermissions($localized));
    }

    public function defaultPermissionKeyBuilder(string $affix, string $separator, string $subject, string $case): string
    {
        return $this->format($case, $affix) . $separator . $this->format($case, $subject);
    }

    public function getDefaultPermissionKeys(string $entity, string | array $affixes): array
    {
        $subject = $this->resolveSubject($entity);

        // Resources: multiple permissions with affixes (view, create, update, etc.)
        if (is_array($affixes)) {
            return collect($affixes)
                ->mapWithKeys(fn (string $affix): array => [
                    $this->format('camel', $affix) => [
                        'key' => $this->buildPermissionKey($entity, $affix, $subject),
                        'label' => $this->getAffixLabel($affix), // . ' ' . $this->resolveEntityLabel($entity),
                    ],
                ])
                ->uniqueStrict()
                ->toArray();
        }

        // Pages/Widgets: single permission with prefix
        $permissionKey = $this->buildPermissionKey($entity, $affixes, $subject);

        return [$permissionKey => $this->getEntityPermissionLabel($entity, $permissionKey)];
    }

    public function getEntitiesPermissions(): ?array
    {
        return collect($this->getAllResourcePermissionsWithLabels())->keys()
            ->merge(collect($this->getPages())->map->permission->keys())
            ->merge(collect($this->getWidgets())->map->permission->keys())
            ->merge(collect($this->getCustomPermissions())->keys())
            ->values()
            ->flatten()
            ->unique()
            ->toArray();
    }

    public function prohibitDestructiveCommands(bool $prohibit = true): void
    {
        Commands\GenerateCommand::prohibit($prohibit);
        Commands\InstallCommand::prohibit($prohibit);
        Commands\PublishCommand::prohibit($prohibit);
        Commands\SeederCommand::prohibit($prohibit);
        Commands\SetupCommand::prohibit($prohibit);
        Commands\SuperAdminCommand::prohibit($prohibit);
    }

    protected function resolveSubject(string $entity): string
    {
        $entity = resolve($entity);

        $subject = match (true) {
            $entity instanceof Resource => Utils::getConfig()->resources->subject,
            $entity instanceof Page => Utils::getConfig()->pages->subject,
            $entity instanceof Widget => Utils::getConfig()->widgets->subject,
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

    private function buildPermissionKey(string $entity, string $affix, string $subject): string
    {
        $permissionConfig = Utils::getConfig()->permissions;

        if ($this->buildPermissionKeyUsing instanceof Closure) {

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

        return $this->defaultPermissionKeyBuilder(
            affix: $affix,
            separator: $permissionConfig->separator,
            subject: $subject,
            case: $permissionConfig->case
        );
    }
}
