<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'shield:generate')]
class MakeShieldGenerateCommand extends Command
{
    use Concerns\CanGeneratePolicy;
    use Concerns\CanManipulateFiles;

    /**
     * The resources to generate permissions or policies for, or should be exclude.
     *
     * @var array
     */
    protected $resources = [];

    /**
     * The pages to generate permissions for, or should be excluded.
     *
     * @var array
     */
    protected $pages = [];

    /**
     * The widgets to generate permissions for, or should be excluded.
     *
     * @var array
     */
    protected $widgets = [];

    protected $generatorOption;

    protected $excludeResources = false;

    protected $excludePages = false;

    protected $excludeWidgets = false;

    protected $onlyResources = false;

    protected $onlyPages = false;

    protected $onlyWidgets = false;

    /**
     * The console command signature.
     *
     * @var string
     */
    public $signature = 'shield:generate
        {--all : Generate permissions/policies for all entities }
        {--option= : Override the config generator option(<fg=green;options=bold>policies_and_permissions,policies,permissions</>)}
        {--resource= : One or many resources separated by comma (,) }
        {--page= : One or many pages separated by comma (,) }
        {--widget= : One or many widgets separated by comma (,) }
        {--exclude : Exclude the given entities during generation }
        {--ignore-config-exclude : Ignore config `<fg=yellow;options=bold>exclude</>` option during generation }
        {--minimal : Output minimal amount of info to console}
    ';
    // {--seeder : Exclude the given entities during generation }
    // the idea is to generate a seeder that can be used on production deployment

    /**
     * The console command description.
     *
     * @var string
     */
    public $description = 'Generate Permissions and/or Policies for Filament entities.';

    public function handle(): int
    {
        $this->determinGeneratorOptionAndEntities();

        if ($this->option('exclude') && blank($this->option('resource')) && blank($this->option('page')) && blank($this->option('widget'))) {
            $this->comment('<fg=red;>No entites provided for the generators ...</>');
            $this->comment('<fg=yellow;options=bold>... generation SKIPPED</>');

            return self::INVALID;
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

        $this->comment('Permission & Policies are generated according to your config or passed options.');
        $this->info('Enjoy!');

        if (cache()->has('shield_general_exclude')) {
            Utils::enableGeneralExclude();
            cache()->forget('shield_general_exclude');
        }

        return self::SUCCESS;
    }

    protected function determinGeneratorOptionAndEntities(): void
    {
        $this->generatorOption = $this->option('option') ?? Utils::getGeneratorOption();

        if ($this->option('ignore-config-exclude') && Utils::isGeneralExcludeEnabled()) {
            cache()->add('shield_general_exclude', true, 3600);
            Utils::disableGeneralExclude();
        }

        $this->resources = (array) explode(',', $this->option('resource'));
        $this->pages = (array) explode(',', $this->option('page'));
        $this->widgets = (array) explode(',', $this->option('widget'));

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
                    return ! in_array($page, $this->pages);
                }

                if ($this->onlyPages) {
                    return in_array($page, $this->pages);
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
                    return ! in_array($widget, $this->widgets);
                }

                if ($this->onlyWidgets) {
                    return in_array($widget, $this->widgets);
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
                    $this->copyStubToApp(static::getPolicyStub($entity['model']), $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
                    FilamentShield::generateForResource($entity);
                }

                if ($this->generatorOption === 'policies') {
                    $this->copyStubToApp(static::getPolicyStub($entity['model']), $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
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
            ->each(function ($page) {
                FilamentShield::generateForPage($page);
            });
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->values()
            ->each(function ($widget) {
                FilamentShield::generateForWidget($widget);
            });
    }

    protected function resourceInfo(array $resources): void
    {
        if ($this->option('minimal')) {
            $this->info('Successfully generated Permissions & Policies.');
        } else {
            $this->info('Successfully generated Permissions & Policies for:');
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
                            )->map(function ($permission, $key) use ($resource) {
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
            $this->info('Successfully generated Page Permissions.');
        } else {
            $this->info('Successfully generated Page Permissions for:');
            $this->table(
                ['#', 'Page', 'Permission'],
                collect($pages)->map(function ($page, $key) {
                    return [
                        '#' => $key + 1,
                        'Page' => Str::replace(config('filament-shield.permission_prefixes.page') . '_', '', $page),
                        'Permission' => $page,
                    ];
                })
            );
        }
    }

    protected function widgetInfo(array $widgets): void
    {
        if ($this->option('minimal')) {
            $this->info('Successfully generated Widget Permissions.');
        } else {
            $this->info('Successfully generated Widget Permissions for:');
            $this->table(
                ['#', 'Widget', 'Permission'],
                collect($widgets)->map(function ($widget, $key) {
                    return [
                        '#' => $key + 1,
                        'Widget' => Str::replace(config('filament-shield.permission_prefixes.widget') . '_', '', $widget),
                        'Permission' => $widget,
                    ];
                })
            );
        }
    }

    protected static function getPolicyStub(string $model): string
    {
        if (Str::is(Str::of(Utils::getAuthProviderFQCN())->afterLast('\\'), $model)) {
            return (string) 'UserPolicy';
        }

        return (string) 'DefaultPolicy';
    }
}
