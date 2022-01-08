<?php

namespace BezhanSalleh\FilamentShield\Commands;

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
        {--O|only : Generate permissions and/or policies `Only` for entities listed in config.}
        {--E|except : Generate permissions and/or policies `Except` for entities listed in config.}
    ';

    public $description = '(Re)Discovers Filament resources and (re)generates Permissions and Policies.';

    public function handle(): int
    {
        if ($this->option('only') && config('filament-shield.only.enabled')) {
            if (! empty($onlyResources = config('filament-shield.only.resources'))) {
                $resources = $this->generateForResources($onlyResources);
                $this->resourceInfo($resources->toArray());
            }

            if (! empty($onlyPages = config('filament-shield.only.pages'))) {
                $pages = $this->generateForPages($onlyPages);
                $this->pageInfo($pages->toArray());
            }

            if (! empty($onlyWidgets = config('filament-shield.only.widgets'))) {
                $widgets = $this->generateForWidgets($onlyWidgets);
                $this->widgetInfo($widgets->toArray());
            }

            if (empty(config('filament-shield.only.resources')) && empty(config('filament-shield.only.pages')) && empty(config('filament-shield.only.widget'))) {
                $this->error('The `only` Config key is empty.');

                return self::INVALID;
            }
        } elseif ($this->option('except')) {
            $exceptResources = config('filament-shield.except.resources');
            $removedExemptedResource = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array(Str::before(Str::afterLast($resource, '\\'), 'Resource'), $exceptResources);
            });

            $resources = $this->generateForResources($removedExemptedResource->toArray());
            $this->resourceInfo($resources->toArray());

            $exceptPages = config('filament-shield.except.pages');
            $removedExemptedPages = collect(Filament::getPages())->filter(function ($page) use ($exceptPages) {
                return ! in_array(Str::afterLast($page, '\\'), $exceptPages);
            });

            $pages = $this->generateForPages($removedExemptedPages->toArray());
            $this->pageInfo($pages->toArray());

            $exceptWidgets = config('filament-shield.except.widgets');
            $removedExemptedWidgets = collect(Filament::getWidgets())->filter(function ($widget) use ($exceptWidgets) {
                return ! in_array(Str::afterLast($widget, '\\'), $exceptWidgets);
            });

            $widgets = $this->generateForWidgets($removedExemptedWidgets->toArray());
            $this->widgetInfo($widgets->toArray());
        } else {
            if (config('filament-shield.entities.resources')) {
                $resources = $this->generateForResources(Filament::getResources());
                $this->resourceInfo($resources->toArray());
            }

            if (config('filament-shield.entities.pages')) {
                $pages = $this->generateForPages(Filament::getPages());
                $this->pageInfo($pages->toArray());
            }

            if (config('filament-shield.entities.widgets')) {
                $widgets = $this->generateForWidgets(Filament::getWidgets());
                $this->widgetInfo($widgets->toArray());
            } else {
                $this->comment('Enable entities in the config file first');
            }
        }

        $this->info('Enjoy!');

        return self::SUCCESS;
    }

    protected function generateForResources(array $resources): Collection
    {
        return  collect($resources)
            ->reduce(function ($entites, $resource) {
                $model = Str::before(Str::afterLast($resource, '\\'), 'Resource');
                $entites[$model] = $model;

                return $entites;
            }, collect())
            ->values()
            ->each(function ($entity) {
                $model = Str::of($entity);
                $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($model), $this->generatePolicyStubVariables($model));
                FilamentShield::generateForResource($model);
            });
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->reduce(function ($transformedWidgets, $widget) {
                $name = Str::snake(Str::after($widget, 'Widgets\\'));
                $transformedWidgets[$name] = $name;

                return $transformedWidgets;
            }, collect())
            ->values()
            ->each(function ($entity) {
                $widget = Str::of($entity);
                FilamentShield::generateForWidget($widget);
            });
    }

    protected function generateForPages(array $pages): Collection
    {
        return collect($pages)
            ->reduce(function ($transformedPages, $page) {
                $name = Str::snake(Str::after($page, 'Pages\\'));
                $transformedPages[$name] = $name;

                return $transformedPages;
            }, collect())
            ->values()
            ->each(function ($entity) {
                $page = Str::of($entity);
                FilamentShield::generateForPage($page);
            });
    }

    protected function resourceInfo(array $resources): void
    {
        $this->info('Successfully generated Permissions & Policies for:');
        $this->table(
            ['#','Resource','Policy','Permissions'],
            collect($resources)->map(function ($resource, $key) {
                return [
                    '#' => $key + 1,
                    'Resource' => $resource,
                    'Policy' => "{$resource}Policy.php",
                    'Permissions' =>
                        implode(',', collect(config('filament-shield.resource_permission_prefixes'))->map(function ($permission, $key) use ($resource) {
                            return $permission.'_'.Str::lower($resource);
                        })->toArray()),

                ];
            })
        );
    }

    protected function pageInfo(array $pages): void
    {
        $this->info('Successfully generated Page Permissions for:');
        $this->table(
            ['#','Page','Permission'],
            collect($pages)->map(function ($page, $key) {
                return [
                    '#' => $key + 1,
                    'Page' => Str::studly($page),
                    'Permission' => config('filament-shield.page_permission_prefix').'_'.Str::snake($page),
                ];
            })
        );
    }

    protected function widgetInfo(array $widgets): void
    {
        $this->info('Successfully generated Widget Permissions for:');
        $this->table(
            ['#','Widget','Permission'],
            collect($widgets)->map(function ($widget, $key) {
                return [
                    '#' => $key + 1,
                    'Widget' => Str::studly($widget),
                    'Permission' => config('filament-shield.widget_permission_prefix').'_'.Str::snake($widget),
                ];
            })
        );
    }
}
