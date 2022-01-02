<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class MakeGenerateShieldCommand extends Command
{
    use Concerns\CanGeneratePolicy;
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:generate';

    public $description = '(Re)Discovers Filament resources and (re)generates Permissions and Policies.';

    public function handle(): int
    {
        $resources = Collect(Filament::getResources())
            ->reduce(function ($entites, $resource) {
                $model = Str::before(Str::afterLast($resource, '\\'), 'Resource');
                $entites[$model] = $model;
                return $entites;
            }, collect())
            ->values()
            ->each(function($entity) {
                $model = Str::of($entity);
                $this->copyStubToApp('DefaultPolicy', $this->generatePolicyPath($model), $this->generatePolicyStubVariables($model));
                FilamentShield::generateFor($model);
            });

        $this->info('Successfully generated policies for '.implode(',', $resources->toArray()));

        $this->info('Successfully generated permissions for '.implode(',', $resources->toArray()));

        $this->comment('Enjoy!');

        return self::SUCCESS;
    }
}
