<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Support\Utils;
use Closure;
use Filament\Pages\BasePage as Page;
use Filament\Resources\Resource;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Gate;
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

    protected bool | Closure $shouldEnforcePolicies = false;

    protected ?array $enforcePoliciesExcept = null;

    public function buildPermissionKeyUsing(Closure $callback): static
    {
        $this->buildPermissionKeyUsing = $callback;

        return $this;
    }

    public function enforcePolicies(bool | Closure $condition = true, ?array $except = null): static
    {
        $this->shouldEnforcePolicies = $condition;
        $this->enforcePoliciesExcept = $except;

        return $this;
    }

    public function registerEnforcedPolicies(): void
    {
        if (! $this->evaluate($this->shouldEnforcePolicies)) {
            return;
        }

        collect($this->getResources())
            ->pluck('modelFqcn')
            ->unique()
            ->reject(fn (string $model): bool => in_array($model, $this->enforcePoliciesExcept ?? [], true))
            ->reject(fn (string $model): bool => array_key_exists($model, Gate::policies()))
            ->each(function (string $model): void {
                $policy = Utils::resolvePolicyFor($model);

                if (class_exists($policy)) {
                    Gate::policy($model, $policy);
                }
            });
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

    /**
     * Format a string value into the specified case.
     *
     * Input is first normalized to PascalCase (handling snake_case, kebab-case,
     * camelCase, UPPER_SNAKE, and space-separated inputs) before applying the
     * target case conversion. This ensures consistent output regardless of
     * the input format.
     */
    protected function format(string $case, string $value): string
    {
        $normalized = $this->normalize($value);

        return match ($case) {
            'kebab' => Str::of($normalized)->kebab()->toString(),
            'pascal' => $normalized,
            'upper_snake' => Str::of($normalized)->snake()->upper()->toString(),
            'lower_snake' => Str::of($normalized)->snake()->lower()->toString(),
            'camel' => Str::of($normalized)->camel()->toString(),
            default => Str::of($normalized)->snake()->toString(),
        };
    }

    /**
     * Normalize a string to PascalCase regardless of its original format.
     *
     * Handles snake_case, kebab-case, camelCase, PascalCase, UPPER_SNAKE_CASE,
     * space-separated, and dot.separated inputs by converting all recognized
     * word boundaries into spaces, then applying studly (PascalCase) conversion.
     *
     * ALL_CAPS input (e.g. UPPER_SNAKE) is lowercased first to prevent
     * Laravel's studly() from treating each character as a separate word.
     */
    protected function normalize(string $value): string
    {
        $withoutSeparators = preg_replace('/[-_.\s]/', '', $value);

        if ($withoutSeparators !== '' && ctype_upper($withoutSeparators)) {
            $value = strtolower($value);
        }

        $value = str_replace(['-', '_', '.'], ' ', $value);

        return Str::of($value)->studly()->toString();
    }

    /**
     * Validate that the configured separator does not conflict with the case format's
     * own delimiter. For example, snake_case uses `_` internally, so using `_` as the
     * separator would make it impossible to distinguish the boundary between the affix
     * and subject in the resulting permission key.
     *
     * @throws InvalidArgumentException When the separator conflicts with the case format.
     */
    protected function validateSeparatorCaseCompatibility(string $separator, string $case): void
    {
        once(function () use ($separator, $case): true {
            $conflicts = [
                '_' => ['snake', 'lower_snake', 'upper_snake'],
                '-' => ['kebab'],
            ];

            if (isset($conflicts[$separator]) && in_array($case, $conflicts[$separator], true)) {
                throw new InvalidArgumentException(
                    "The separator \"{$separator}\" cannot be used with the \"{$case}\" case format because " .
                    "it conflicts with the case's own delimiter, making it impossible to distinguish " .
                    'the affix from the subject in permission keys.'
                );
            }

            return true;
        });
    }

    private function buildPermissionKey(string $entity, string $affix, string $subject): string
    {
        $permissionConfig = Utils::getConfig()->permissions;

        $this->validateSeparatorCaseCompatibility($permissionConfig->separator, $permissionConfig->case);

        if ($this->buildPermissionKeyUsing instanceof Closure) {

            /** @var ?string $result */
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

            // Non-null return means the closure handled it; null falls through to default
            if ($result !== null) {
                return $result;
            }
        }

        return $this->defaultPermissionKeyBuilder(
            affix: $affix,
            separator: $permissionConfig->separator,
            subject: $subject,
            case: $permissionConfig->case
        );
    }
}
