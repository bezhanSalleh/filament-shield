<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use Illuminate\Support\Str;

trait CanGeneratePolicy
{
    protected function generateModelName(string $name): string
    {
        return (string) Str::of($name)->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->studly()
            ->replace('/', '\\');
    }

    protected function generatePolicyPath(string $model): string
    {
        $basePolicyPath = app_path(
            (string) Str::of($model)
            ->prepend('Policies\\')
            ->replace('\\', DIRECTORY_SEPARATOR),
        );

        return "{$basePolicyPath}Policy.php";
    }

    protected function generatePolicyStubVariables(string $model): array
    {
        $defaultPermissions = collect(config('filament-shield.permission_prefixes.resource'))
            ->reduce(function ($gates, $permission) use ($model) {
                $gates[Str::studly($permission)] = $permission.'_'.Str::lower($model);

                return $gates;
            }, []);

        $defaultPermissions['modelPolicy'] = "{$model}Policy";

        return $defaultPermissions;
    }
}
