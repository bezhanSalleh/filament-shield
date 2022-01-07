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
        {--O|only : Generate permissions and policies `Only` for these.}
        {--E|except : Generate permissions and policies `Except` for these.}
    ';

    public $description = '(Re)Discovers Filament resources and (re)generates Permissions and Policies.';


    public function handle(): int
    {
        if ($this->option('only')) {
            if (collect($only = config('filament-shield.only'))->isNotEmpty()) {
                $resources = $this->generatePermissionsAndPolicies($only);
            } else {
                $this->error('The `only` Config key is empty.');
                return self::INVALID;
            }
        }
        else if ($this->option('except')) {
            if (collect($except = config('filament-shield.except'))->isNotEmpty()) {
                $removedExemptedResource = collect(Filament::getResources())->filter(function ($resource) use($except){
                    return !in_array(Str::before(Str::afterLast($resource, '\\'), 'Resource'), $except);
                });
                $resources = $this->generatePermissionsAndPolicies($removedExemptedResource->toArray());
            } else {
                $this->error('The `except` Config key is empty.');
                return self::INVALID;
            }
        } else {
            $resources = $this->generatePermissionsAndPolicies(Filament::getResources());
        }


        $this->info('Successfully generated policies for '.implode(',', $resources->toArray()));

        $this->info('Successfully generated permissions for '.implode(',', $resources->toArray()));

        $this->line('Enjoy!');

        return self::SUCCESS;
    }

    protected function generatePermissionsAndPolicies(array $resources): Collection
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
                FilamentShield::generateFor($model);
            });
    }
}
