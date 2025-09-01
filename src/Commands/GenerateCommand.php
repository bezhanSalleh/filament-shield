<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanGeneratePolicy;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanGenerateRelationshipsForTenancy;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\Select;

#[AsCommand(name: 'shield:generate', description: 'Generate Permissions and/or Policies for Filament entities.')]
class GenerateCommand extends Command
{
    use CanGeneratePolicy;
    use CanGenerateRelationshipsForTenancy;
    use CanManipulateFiles;
    use Prohibitable;

    protected array $resources = [];

    protected array $pages = [];

    protected array $widgets = [];

    protected string $generatorOption;

    protected bool $excludeResources = false;

    protected bool $excludePages = false;

    protected bool $excludeWidgets = false;

    protected bool $onlyResources = false;

    protected bool $onlyPages = false;

    protected bool $onlyWidgets = false;

    protected bool $ignoreConfigExclude = false;

    protected array $counts = [
        'entities' => 0,
        'policies' => 0,
        'permissions' => 0,
    ];

    /** @var string */
    public $signature = 'shield:generate
        {--all : Generate permissions/policies for all entities }
        {--option= : Override the config generator option(<fg=green;options=bold>policies_and_permissions,policies,permissions and tenant_relationships</>)}
        {--resource= : One or many resources separated by comma (,) }
        {--page= : One or many pages separated by comma (,) }
        {--widget= : One or many widgets separated by comma (,) }
        {--exclude : Exclude the given entities during generation }
        {--ignore-config-exclude : Ignore config `<fg=yellow;options=bold>exclude</>` option during generation }
        {--ignore-existing-policies : Ignore generating policies that already exist }
        {--panel= : Panel ID to get the components(resources, pages, widgets)}
        {--relationships : Generate relationships for the given panel, only works if the panel has tenancy enabled}
    ';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $panel = $this->option('panel') ?: Select(
            label: 'Which panel do you want to generate permissions/policies for?',
            options: collect(Filament::getPanels())->keys()->toArray()
        );

        Filament::setCurrentPanel(Filament::getPanel($panel));

        $this->determinGeneratorOptionAndEntities();

        if ($this->option('exclude') && blank($this->option('resource')) && blank($this->option('page')) && blank($this->option('widget'))) {
            $this->components->error('No entites provided for the generators ...');
            $this->components->alert('Generation skipped');

            $this->resetConfigExclusionCondition($this->ignoreConfigExclude);

            return Command::INVALID;
        }

        if (filled($this->option('resource')) || $this->option('all')) {
            $resources = $this->generateForResources($this->generatableResources());
            $this->resourceInfo($resources->toArray());
        }

        if (filled($this->option('page')) || $this->option('all')) {
            $pages = $this->generateForPages($this->generatablePages());
            $this->pageInfo($pages->toArray());
        }

        if (filled($this->option('widget')) || $this->option('all')) {
            $widgets = $this->generateForWidgets($this->generatableWidgets());
            $this->widgetInfo($widgets->toArray());
        }

        $this->resetConfigExclusionCondition($this->ignoreConfigExclude);

        if (! $this->option('verbose')) {
            $this->components->twoColumnDetail('# Policies generated', (string) $this->counts['policies']);
            $this->components->twoColumnDetail('# Permissions generated', (string) $this->counts['permissions']);
            $this->components->twoColumnDetail('# Entities (Resources, Pages, Widgets) processed', (string) $this->counts['entities']);
        }

        if (Filament::hasTenancy() && Utils::isTenancyEnabled() && $this->option('relationships')) {
            $this->generateRelationships(Filament::getPanel($panel));
            $this->components->info('Successfully generated relationships for the given panel.');
        }

        return Command::SUCCESS;
    }

    protected function determinGeneratorOptionAndEntities(): void
    {
        $this->generatorOption = $this->option('option') ?? FilamentShield::getGeneratorOption();

        $this->ignoreConfigExclude = $this->option('ignore-config-exclude') ?? false;

        if ($this->ignoreConfigExclude && Utils::isGeneralExcludeEnabled()) {
            Utils::disableGeneralExclude();
        }

        $this->resources = filled($this->option('resource')) ? explode(',', $this->option('resource')) : [];
        $this->pages = filled($this->option('page')) ? explode(',', $this->option('page')) : [];
        $this->widgets = filled($this->option('widget')) ? explode(',', $this->option('widget')) : [];

        $this->excludeResources = $this->option('exclude') && filled($this->option('resource'));
        $this->excludePages = $this->option('exclude') && filled($this->option('page'));
        $this->excludeWidgets = $this->option('exclude') && filled($this->option('widget'));

        $this->onlyResources = ! $this->option('exclude') && filled($this->option('resource'));
        $this->onlyPages = ! $this->option('exclude') && filled($this->option('page'));
        $this->onlyWidgets = ! $this->option('exclude') && filled($this->option('widget'));
    }

    protected function generatableResources(): ?array
    {
        return collect(FilamentShield::getResources())
            ->filter(function (array $resource): bool {
                if ($this->excludeResources) {
                    return ! in_array(Str::of($resource['resourceFqcn'])->afterLast('\\'), $this->resources);
                }

                if ($this->onlyResources) {
                    return in_array(Str::of($resource['resourceFqcn'])->afterLast('\\'), $this->resources);
                }

                return true;
            })
            ->toArray();
    }

    protected function generatablePages(): ?array
    {
        return collect(FilamentShield::getPages())
            ->filter(function (array $page): bool {
                if ($this->excludePages) {
                    return ! in_array(Str::of($page['pageFqcn'])->afterLast('\\'), $this->pages);
                }

                if ($this->onlyPages) {
                    return in_array(Str::of($page['pageFqcn'])->afterLast('\\'), $this->pages);
                }

                return true;
            })
            ->toArray();
    }

    protected function generatableWidgets(): ?array
    {
        return collect(FilamentShield::getWidgets())
            ->filter(function (array $widget): bool {
                if ($this->excludeWidgets) {
                    return ! in_array(Str::of($widget['class'])->afterLast('\\'), $this->widgets);
                }

                if ($this->onlyWidgets) {
                    return in_array(Str::of($widget['class'])->afterLast('\\'), $this->widgets);
                }

                return true;
            })
            ->toArray();
    }

    protected function generateForResources(array $resources): Collection
    {
        return collect($resources)
            ->values()
            ->each(function (array $entity): void {

                if ($this->generatorOption === 'policies_and_permissions') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['modelFqcn']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                    FilamentShield::generateForResource($entity['resourceFqcn']);
                }

                if ($this->generatorOption === 'policies') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['modelFqcn']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                }

                if ($this->generatorOption === 'permissions') {
                    FilamentShield::generateForResource($entity['resourceFqcn']);
                }
            });
    }

    protected function generateForPages(array $pages): Collection
    {
        return collect($pages)
            ->values()
            ->each(function (array $page): void {
                FilamentShield::generateForPage(array_key_first($page['permissions']));
            });
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->values()
            ->each(function (array $widget): void {
                FilamentShield::generateForWidget(array_key_first($widget['permissions']));
            });
    }

    protected function resourceInfo(array $resources): void
    {
        collect($resources)->map(function (array $resource): void {
            $this->counts['entities']++;
            if ($this->generatorOption !== 'permissions') {
                $this->counts['policies']++;
            }

            if ($this->generatorOption !== 'policies') {
                $this->counts['permissions'] += count(FilamentShield::getResourcePermissions($resource['resourceFqcn']));

            }
        });

        if ($this->option('verbose')) {
            $this->components->info('Successfully generated Permissions & Policies for:');
            $this->table(
                ['#', 'Resource', 'Policy', 'Permissions'],
                collect($resources)->map(fn (array $resource, int $key): array => [
                    '#' => $key + 1,
                    'Resource' => $resource['model'],
                    'Policy' => "{$resource['model']}Policy.php" . ($this->generatorOption !== 'permissions' ? ' ✅' : ' ❌'),
                    'Permissions' => implode(
                        ',' . PHP_EOL,
                        FilamentShield::getResourcePermissions($resource['resourceFqcn'])
                    ) . ($this->generatorOption !== 'policies' ? ' ✅' : ' ❌'),
                ])
            );
        }
    }

    protected function pageInfo(array $pages): void
    {
        $this->counts['entities'] += count($pages);
        $this->counts['permissions'] += count($pages);

        if ($this->option('verbose')) {
            $this->components->info('Successfully generated Page Permissions for:');
            $this->table(
                ['#', 'Page', 'Permission'],
                collect($pages)->map(fn (array $page, int $key): array => [
                    '#' => $key + 1,
                    'Page' => $page['class'],
                    'Permission' => $page['permission'],
                ])
            );
        }
    }

    protected function widgetInfo(array $widgets): void
    {
        $this->counts['entities'] += count($widgets);
        $this->counts['permissions'] += count($widgets);

        if ($this->option('verbose')) {
            $this->components->info('Successfully generated Widget Permissions for:');
            $this->table(
                ['#', 'Widget', 'Permission'],
                collect($widgets)->map(fn (array $widget, int $key): array => [
                    '#' => $key + 1,
                    'Widget' => $widget['class'],
                    'Permission' => $widget['permission'],
                ])
            );
        }
    }

    protected static function getPolicyStub(string $model): string
    {
        if (resolve($model) instanceof Authenticatable) {
            return 'UserPolicy';
        }

        return 'DefaultPolicy';
    }

    protected function resetConfigExclusionCondition(bool $condition): void
    {
        if ($condition) {
            Utils::enableGeneralExclude();
        }
    }
}
