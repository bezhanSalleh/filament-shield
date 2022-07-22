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

    protected function generatePolicyPath(array $entity): string
    {
        if ($entity['model'] === 'Role') {
            $basePolicyPath = app_path(
                (string) Str::of($entity['model'])
                ->prepend('Policies\\')
                ->replace('\\', DIRECTORY_SEPARATOR),
            );

            return "{$basePolicyPath}Policy.php";
        }

        $path = (new \ReflectionClass($entity['resource']::getModel()))->getFileName();

        $basePath = Str::of($path)
            ->replace('Models', 'Policies')
            ->replaceLast('.php', 'Policy.php')
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->value;

        return $basePath;
    }

    protected function generatePolicyStubVariables(array $entity): array
    {
        $stubVariables = collect(config('filament-shield.permission_prefixes.resource'))
            ->reduce(function ($gates, $permission) use ($entity) {
                $gates[Str::studly($permission)] = $permission.'_'.$entity['permission'];

                return $gates;
            }, collect())->toArray();

        $stubVariables['auth_model_fqcn'] = config('filament-shield.auth_provider_model.fqcn');
        $stubVariables['auth_model_name'] = Str::of($stubVariables['auth_model_fqcn'])->afterLast('\\');

        $namespace = (new \ReflectionClass($entity['resource']::getModel()))
            ->getNamespaceName();

        $stubVariables['namespace'] = $entity['model'] === 'Role'
            ? 'App\Policies'
            : Str::of($namespace)->replace('Models', 'Policies')->value;

        $stubVariables['modelPolicy'] = "{$entity['model']}Policy";

        return $stubVariables;
    }
}
