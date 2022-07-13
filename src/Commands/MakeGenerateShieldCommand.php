<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Contracts\HasPermissions;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MakeGenerateShieldCommand extends Command
{
    use Concerns\CanGeneratePolicy;
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:generate
        {--E|exclude : Generate permissions w/o policies except those `exclude`d.}
    ';

    public $description = '(Re)Discovers Filament resources and (re)generates Permissions and Policies.';

    public function handle(): int
    {
        $resources = collect(Filament::getResources());
        $pages = collect(Filament::getPages());
        $widgets = collect(Filament::getWidgets());

        if ($this->isExcludeEnabled()) {
            $excepts = config('filament-shield.exclude.pages');
            $pages = collect($pages)
                ->filter(
                    function ($page) use ($excepts) {
                        return !in_array(Str::afterLast($page, '\\'), $excepts);
                    }
                );

            $excepts = config('filament-shield.exclude.widgets');
            $widgets = collect($widgets)
                ->filter(
                    function ($widget) use ($excepts) {
                        return !in_array(Str::afterLast($widget, '\\'), $excepts);
                    }
                );
        }

        if (config('filament-shield.entities.resources')) {
            $resources = $resources
                ->filter(
                    fn($resource) => in_array(HasPermissions::class, class_implements($resource))
                );

            $resources = $this->generateForResources($resources->toArray());

            $this->resourceInfo($resources->toArray());
        }

        if (config('filament-shield.entities.pages')) {
            $pages = $this->generateForPages($pages);

            $this->pageInfo($pages->toArray());
        }

        if (config('filament-shield.entities.widgets')) {
            $widgets = $this->generateForWidgets($widgets);

            $this->widgetInfo($widgets->toArray());
        } else {
            $this->comment('Please enable `entities` from config first.');
        }

        $this->info('Enjoy!');

        return self::SUCCESS;
    }

    protected function isExcludeEnabled(): bool
    {
        return $this->option('exclude') && config('filament-shield.exclude.enabled');
    }

    protected function generateForResources(array $resources): Collection
    {
        return collect($resources)
            ->reduce(
                function ($entities, $resource) {
                    $model = $resource::getModel();
                    $model = class_basename($model);
                    $entities[$model] = $resource::permissions();

                    return $entities;
                },
                collect()
            )
            ->each(
                function ($permissions, $entity) {
                    if (config('filament-shield.resources_generator_option') === 'policies_and_permissions') {
                        $this->copyStubToApp(
                            'DefaultPolicy',
                            $this->generatePolicyPath($entity),
                            $this->generatePolicyStubVariables($entity)
                        );

                        FilamentShield::generateForResource($entity, $permissions);
                    }

                    if (config('filament-shield.resources_generator_option') === 'policies') {
                        $this->copyStubToApp(
                            'DefaultPolicy',
                            $this->generatePolicyPath($entity),
                            $this->generatePolicyStubVariables($entity)
                        );
                    }

                    if (config('filament-shield.resources_generator_option') === 'permissions') {
                        FilamentShield::generateForResource($entity, $permissions);
                    }
                }
            );
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->reduce(
                function ($transformedWidgets, $widget) {
                    $name = Str::of($widget)->after('Widgets\\')->replace('\\', '')->snake();
                    $transformedWidgets["{$name}"] = "{$name}";

                    return $transformedWidgets;
                },
                collect()
            )
            ->values()
            ->each(
                function ($entity) {
                    $widget = Str::of($entity);
                    FilamentShield::generateForWidget($widget);
                }
            );
    }

    protected function generateForPages(array $pages): Collection
    {
        return collect($pages)
            ->reduce(
                function ($transformedPages, $page) {
                    $name = Str::of($page)->after('Pages\\')->replace('\\', '')->snake();
                    $transformedPages["{$name}"] = "{$name}";

                    return $transformedPages;
                },
                collect()
            )->values()
            ->each(
                function ($entity) {
                    $page = Str::of($entity);
                    FilamentShield::generateForPage($page);
                }
            );
    }

    protected function resourceInfo(array $resources): void
    {
        $this->info('Successfully generated Permissions & Policies for:');

        $rows = [];

        foreach ($resources as $resource => $prefixes) {
            $permissions = collect($prefixes)
                ->map(fn($permission) => $permission.'_'.Str::lower($resource))
                ->implode(',');

            $rows[] = [
                '#' => count($rows) + 1,
                'Resource' => $resource,
                'Policy' => "{$resource}Policy.php".(
                    config('filament-shield.resources_generator_option') !== 'permissions'
                        ? ' ✅'
                        : ' ❌'
                    ),
                'Permissions' => $permissions.(
                    config('filament-shield.resources_generator_option') !== 'policies'
                        ? ' ✅'
                        : ' ❌'
                    ),
            ];
        }

        $this->table(['#', 'Resource', 'Policy', 'Permissions'], $rows);
    }

    protected function pageInfo(array $pages): void
    {
        $this->info('Successfully generated Page Permissions for:');
        $this->table(
            ['#', 'Page', 'Permission'],
            collect($pages)->map(function ($page, $key) {
                return [
                    '#' => $key + 1,
                    'Page' => Str::studly($page),
                    'Permission' => config('filament-shield.prefixes.page').'_'.Str::snake($page),
                ];
            })
        );
    }

    protected function widgetInfo(array $widgets): void
    {
        $this->info('Successfully generated Widget Permissions for:');
        $this->table(
            ['#', 'Widget', 'Permission'],
            collect($widgets)->map(function ($widget, $key) {
                return [
                    '#' => $key + 1,
                    'Widget' => Str::studly($widget),
                    'Permission' => config('filament-shield.prefixes.widget').'_'.Str::snake($widget),
                ];
            })
        );
    }
}
