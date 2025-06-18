<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\Select;

#[AsCommand(name: 'shield:generate')]
class GenerateCommand extends Command
{
    use Concerns\CanBeProhibitable;
    use Concerns\CanGeneratePolicy;
    use Concerns\CanGenerateRelationshipsForTenancy;
    use Concerns\CanManipulateFiles;

    /**
     * The resources to generate permissions or policies for, or should be exclude.
     */
    protected array $resources = [];

    /**
     * The pages to generate permissions for, or should be excluded.
     */
    protected array $pages = [];

    /**
     * The widgets to generate permissions for, or should be excluded.
     */
    protected array $widgets = [];

    protected string $generatorOption;

    protected bool $excludeResources = false;

    protected bool $excludePages = false;

    protected bool $excludeWidgets = false;

    protected bool $onlyResources = false;

    protected bool $onlyPages = false;

    protected bool $onlyWidgets = false;

    protected bool $ignoreConfigExclude = false;

    /** @var string */
    public $signature = 'shield:generate
        {--all : Generate permissions/policies for all entities }
        {--option= : Override the config generator option(<fg=green;options=bold>policies_and_permissions,policies,permissions and tenant_relationships</>)}
        {--resource= : One or many resources separated by comma (,) }
        {--page= : One or many pages separated by comma (,) }
        {--widget= : One or many widgets separated by comma (,) }
        {--exclude : Exclude the given entities during generation }
        {--ignore-config-exclude : Ignore config `<fg=yellow;options=bold>exclude</>` option during generation }
        {--minimal : Output minimal amount of info to console}
        {--ignore-existing-policies : Ignore generating policies that already exist }
        {--panel= : Panel ID to get the components(resources, pages, widgets)}
        {--relationships : Generate relationships for the given panel, only works if the panel has tenancy enabled}
    ';

    /** @var string */
    public $description = 'Generate Permissions and/or Policies for Filament entities.';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $panel = $this->option('panel')
            ? $this->option('panel')
            : Select(
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

        if (Filament::hasTenancy() && Utils::isTenancyEnabled() && ($this->option('relationships') || $this->option('all'))) {
            $this->generateRelationships(Filament::getPanel($panel));
            $this->components->info('Successfully generated relationships for the given panel.');
        }

        return Command::SUCCESS;
    }

    protected function determinGeneratorOptionAndEntities(): void
    {
        $this->generatorOption = $this->option('option') ?? Utils::getGeneratorOption();

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
            ->filter(function ($resource) {
                if ($this->excludeResources) {
                    return ! in_array(Str::of($resource['fqcn'])->afterLast('\\'), $this->resources);
                }

                if ($this->onlyResources) {
                    return in_array(Str::of($resource['fqcn'])->afterLast('\\'), $this->resources);
                }

                return true;
            })
            ->toArray();
    }

    protected function generatablePages(): ?array
    {
        return collect(FilamentShield::getPages())
            ->filter(function ($page) {
                if ($this->excludePages) {
                    return ! in_array($page['class'], $this->pages);
                }

                if ($this->onlyPages) {
                    return in_array($page['class'], $this->pages);
                }

                return true;
            })
            ->toArray();
    }

    protected function generatableWidgets(): ?array
    {
        return collect(FilamentShield::getWidgets())
            ->filter(function ($widget) {
                if ($this->excludeWidgets) {
                    return ! in_array($widget['class'], $this->widgets);
                }

                if ($this->onlyWidgets) {
                    return in_array($widget['class'], $this->widgets);
                }

                return true;
            })
            ->toArray();
    }

    protected function generateForResources(array $resources): Collection
    {
        return collect($resources)
            ->values()
            ->each(function ($entity) {
                if ($this->generatorOption === 'policies_and_permissions') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['model']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                    FilamentShield::generateForResource($entity);
                }

                if ($this->generatorOption === 'policies') {
                    $policyPath = $this->generatePolicyPath($entity);
                    /** @phpstan-ignore-next-line */
                    if (! $this->option('ignore-existing-policies') || ($this->option('ignore-existing-policies') && ! $this->fileExists($policyPath))) {
                        $this->copyStubToApp(static::getPolicyStub($entity['model']), $policyPath, $this->generatePolicyStubVariables($entity));
                    }
                }

                if ($this->generatorOption === 'permissions') {
                    FilamentShield::generateForResource($entity);
                }
            });
    }

    protected function generateForPages(array $pages): Collection
    {
        return collect($pages)
            ->values()
            ->each(fn (array $page) => FilamentShield::generateForPage($page['permission']));
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->values()
            ->each(fn (array $widget) => FilamentShield::generateForWidget($widget['permission']));
    }

    protected function resourceInfo(array $resources): void
    {
        if ($this->option('minimal')) {
            $this->components->info('Successfully generated Permissions & Policies.');
        } else {
            $this->components->info('Successfully generated Permissions & Policies for:');
            $this->table(
                ['#', 'Resource', 'Policy', 'Permissions'],
                collect($resources)->map(function ($resource, $key) {
                    return [
                        '#' => $key + 1,
                        'Resource' => $resource['model'],
                        'Policy' => "{$resource['model']}Policy.php" . ($this->generatorOption !== 'permissions' ? ' ✅' : ' ❌'),
                        'Permissions' => implode(
                            ',' . PHP_EOL,
                            collect(
                                Utils::getResourcePermissionPrefixes($resource['fqcn'])
                            )->map(function ($permission) use ($resource) {
                                return $permission . '_' . $resource['resource'];
                            })->toArray()
                        ) . ($this->generatorOption !== 'policies' ? ' ✅' : ' ❌'),
                    ];
                })
            );
        }
    }

    protected function pageInfo(array $pages): void
    {
        if ($this->option('minimal')) {
            $this->components->info('Successfully generated Page Permissions.');
        } else {
            $this->components->info('Successfully generated Page Permissions for:');
            $this->table(
                ['#', 'Page', 'Permission'],
                collect($pages)->map(function ($page, $key) {
                    return [
                        '#' => $key + 1,
                        'Page' => $page['class'],
                        'Permission' => $page['permission'],
                    ];
                })
            );
        }
    }

    protected function widgetInfo(array $widgets): void
    {
        if ($this->option('minimal')) {
            $this->components->info('Successfully generated Widget Permissions.');
        } else {
            $this->components->info('Successfully generated Widget Permissions for:');
            $this->table(
                ['#', 'Widget', 'Permission'],
                collect($widgets)->map(function ($widget, $key) {
                    return [
                        '#' => $key + 1,
                        'Widget' => $widget['class'],
                        'Permission' => $widget['permission'],
                    ];
                })
            );
        }
    }

    protected static function getPolicyStub(string $model): string
    {
        if (Str::is(Str::of(Utils::getAuthProviderFQCN())->afterLast('\\'), $model)) {
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
