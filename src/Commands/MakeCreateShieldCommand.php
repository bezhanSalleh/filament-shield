<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Contracts\HasPermissions;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeCreateShieldCommand extends Command
{
    use Concerns\CanGeneratePolicy;
    use Concerns\CanManipulateFiles;
    use Concerns\CanValidateInput;

    public $signature = 'shield:create {resource?}';

    public $description = 'Create Permissions and/or Policy for the given Filament Resource Model';

    public function handle(): int
    {
        $resource = $this->argument('resource')
            ?? $this->askRequired('Resource (e.g. `Filament\PostResource or PostResource`)', 'resource');

        if (!Str::contains($resource, '\\')) {
            $resource = collect(Filament::getResources())
                ->first(fn($item) => Str::endsWith($item, $resource));
        }

        if (!class_exists($resource)) {
            $this->error("A resource with the name '{$resource}' could not be found.");

            return static::INVALID;
        }

        $model = class_basename($resource::getModel());

        $choice = $this->choice('What would you like to Generate for the Resource?', [
            "Permissions & Policy",
            "Only Permissions",
            "Only Policy",
        ], 0);

        if ($choice === "Permissions & Policy") {
            if (!$this->hasPermissions($resource)) {
                $this->error(
                    "The resource does not implement the 'HasPermissions' contract so it cannot generate the permissions."
                );

                return static::INVALID;
            }

            if ($this->checkForCollision([$this->generatePolicyPath($model)])) {
                return static::INVALID;
            }

            $this->copyStubToApp(
                'DefaultPolicy',
                $this->generatePolicyPath($model),
                $this->generatePolicyStubVariables($model)
            );

            $this->info("Successfully generated {$model}Policy for {$model}Resource");

            FilamentShield::generateForResource($model, $resource::permissions());

            $this->info("Successfully generated Permissions for {$model}Resource");
        }

        if ($choice === "Only Permissions") {
            if (!$this->hasPermissions($resource)) {
                $this->error(
                    "The resource does not implement the 'HasPermissions' contract so it cannot generate the permissions."
                );

                return static::INVALID;
            }

            FilamentShield::generateForResource($model, $resource::permissions());

            $this->info("Successfully generated Permissions for {$model}Resource");
        }

        if ($choice === "Only Policy") {
            if ($this->checkForCollision([$this->generatePolicyPath($model)])) {
                return static::INVALID;
            }

            $this->copyStubToApp(
                'DefaultPolicy',
                $this->generatePolicyPath($model),
                $this->generatePolicyStubVariables($model)
            );

            $this->info("Successfully generated {$model}Policy for {$model}Resource");
        }

        return self::SUCCESS;
    }

    protected function hasPermissions($resource): bool
    {
        return in_array(HasPermissions::class, class_implements($resource));
    }
}
