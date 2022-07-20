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
        {--E|exclude : Generate permissions w/o policies except those `exclude`d.}
    ';

    public $description = '(Re)Discovers Filament resources and (re)generates Permissions and Policies.';

    public function handle(): int
    {
        if ($this->option('exclude') && config('filament-shield.exclude.enabled')) {
            $exceptResources = config('filament-shield.exclude.resources');
            $removedExcludedResources = collect(Filament::getResources())->filter(function ($resource) use ($exceptResources) {
                return ! in_array(Str::of($resource)->afterLast('\\'), $exceptResources);
            });
            if (config('filament-shield.entities.resources')) {
                $resources = $this->generateForResources($removedExcludedResources->toArray());
                $this->resourceInfo($resources->toArray());
            }
            $exceptPages = config('filament-shield.exclude.pages');
            $removedExcludedPages = collect(Filament::getPages())->filter(function ($page) use ($exceptPages) {
                return ! in_array(Str::afterLast($page, '\\'), $exceptPages);
            });
            if (config('filament-shield.entities.pages')) {
                $pages = $this->generateForPages($removedExcludedPages->toArray());
                $this->pageInfo($pages->toArray());
            }
            $exceptWidgets = config('filament-shield.exclude.widgets');
            $removedExcludedWidgets = collect(Filament::getWidgets())->filter(function ($widget) use ($exceptWidgets) {
                return ! in_array(Str::afterLast($widget, '\\'), $exceptWidgets);
            });
            if (config('filament-shield.entities.pages')) {
                $widgets = $this->generateForWidgets($removedExcludedWidgets->toArray());
                $this->widgetInfo($widgets->toArray());
            }
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
                $this->comment('Please enable `entities` from config first.');
            }
        }
        $this->info('Enjoy!');

        return self::SUCCESS;
    }

    protected function generateForResources(array $resources): Collection
    {
        return  collect($resources)
            ->reduce(function ($entites, $resource) {
                $permission = Str::of($resource)->afterLast('Resources\\')->before('Resource')->replace('\\','')->headline()->snake()->replace('_','::')->toString();
                $entites[$permission] =[
                    'permission' => $permission,
                    'model' => Str::of($resource::getModel())->afterLast('\\')->toString(),
                    'resource' => $resource,
                ];

                return $entites;
            }, collect())
            ->values()
            ->each(function ($entity) {

                if (config('filament-shield.generator.option') === 'policies_and_permissions') {
                    $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
                    FilamentShield::generateForResource($entity['permission']);
                }

                if (config('filament-shield.generator.option') === 'policies') {
                    $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
                }

                if (config('filament-shield.generator.option') === 'permissions') {
                    FilamentShield::generateForResource($entity['permission']);
                }
            });
    }

    protected function generateForWidgets(array $widgets): Collection
    {
        return collect($widgets)
            ->reduce(function ($transformedWidgets, $widget) {
                $name = Str::of($widget)->after('Widgets\\')->replace('\\', '')->snake();
                $transformedWidgets["{$name}"] = "{$name}";

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
        return collect($pages)->reduce(function ($transformedPages, $page) {
            $name = Str::of($page)->after('Pages\\')->replace('\\', '')->snake();
            $transformedPages["{$name}"] = "{$name}";

            return $transformedPages;
        }, collect())->values()
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
                    'Resource' => $resource['model'],
                    'Policy' => "{$resource['model']}Policy.php". (config('filament-shield.generator.option') !== 'permissions' ? ' ✅' : ' ❌') ,
                    'Permissions' =>
                        implode(',', collect(config('filament-shield.permission_prefixes.resource'))->map(function ($permission, $key) use ($resource) {
                            return $permission.'_'.Str::lower($resource['permission']);
                        })->toArray()) . (config('filament-shield.generator.option') !== 'policies' ? ' ✅' : ' ❌'),
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
                    'Permission' => config('filament-shield.permission_prefixes.page').'_'.Str::snake($page),
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
                    'Permission' => config('filament-shield.permission_prefixes.widget').'_'.Str::snake($widget),
                ];
            })
        );
    }
}
