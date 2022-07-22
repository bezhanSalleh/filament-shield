<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class MakeShieldGenerateCommand extends Command
{
    use Concerns\CanGeneratePolicy;
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:generate';

    public $description = '(Re)Discovers Filament entities and (re)generates Permissions and/or Policies.';

    public function handle(): int
    {
        if (config('filament-shield.entities.resources')) {
            $resources = $this->generateForResources(FilamentShield::getResources());
            $this->resourceInfo($resources->toArray());
        }

        if (config('filament-shield.entities.pages')) {
            $pages = $this->generateForPages(FilamentShield::getPages());
            $this->pageInfo($pages->toArray());
        }

        if (config('filament-shield.entities.widgets')) {
            $widgets = $this->generateForWidgets(FilamentShield::getWidgets());
            $this->widgetInfo($widgets->toArray());
        } else {
            $this->comment('Please enable `entities` from config first.');
        }

        $this->info('Enjoy!');

        return self::SUCCESS;
    }

    protected function generateForResources(array $resources): Collection
    {
        return  collect($resources)
            ->values()
            ->each(function ($entity) {
                if (config('filament-shield.generator.option') === 'policies_and_permissions') {
                    $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
                    FilamentShield::generateForResource($entity['resource']);
                }

                if (config('filament-shield.generator.option') === 'policies') {
                    $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($entity), $this->generatePolicyStubVariables($entity));
                }

                if (config('filament-shield.generator.option') === 'permissions') {
                    FilamentShield::generateForResource($entity['resource']);
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
                            return $permission.'_'.$resource['resource'];
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
                    'Permission' => $page,
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
                    'Permission' => $widget,
                ];
            })
        );
    }
}
