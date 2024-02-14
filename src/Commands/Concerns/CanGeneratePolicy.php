<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
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
        $path = (new \ReflectionClass($entity['fqcn']::getModel()))->getFileName();

        $policyPath = Str::of(config('filament-shield.generator.policy_directory', 'Policies'))
            ->replace('\\', DIRECTORY_SEPARATOR);

        if (Str::of($path)->contains(['vendor', 'src'])) {
            $basePolicyPath = app_path(
                (string) Str::of($entity['model'])
                    ->prepend($policyPath->append('\\'))
                    ->replace('\\', DIRECTORY_SEPARATOR),
            );

            return "{$basePolicyPath}Policy.php";
        }

        /** @phpstan-ignore-next-line */
        $basePath = Str::of($path)
            ->replace('Models', $policyPath)
            ->replaceLast('.php', 'Policy.php')
            ->replace('\\', DIRECTORY_SEPARATOR);

        return $basePath;
    }

    protected function generatePolicyStubVariables(array $entity): array
    {
        $stubVariables = collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->reduce(function ($gates, $permission) use ($entity) {
                $gates[Str::studly($permission)] = $permission . '_' . $entity['resource'];

                return $gates;
            }, collect())->toArray();

        $stubVariables['auth_model_fqcn'] = Utils::getAuthProviderFQCN();
        $stubVariables['auth_model_name'] = Str::of($stubVariables['auth_model_fqcn'])->afterLast('\\');
        $stubVariables['auth_model_variable'] = Str::of($stubVariables['auth_model_name'])->camel();

        $reflectionClass = new \ReflectionClass($entity['fqcn']::getModel());
        $namespace = $reflectionClass->getNamespaceName();
        $path = $reflectionClass->getFileName();

        $stubVariables['namespace'] = Str::of($path)->contains(['vendor', 'src'])
            ? 'App\\' . Utils::getPolicyNamespace()
            : Str::of($namespace)->replace('Models', Utils::getPolicyNamespace()); /** @phpstan-ignore-line */
        $stubVariables['model_name'] = $entity['model'];
        $stubVariables['model_fqcn'] = $namespace . '\\' . $entity['model'];
        $stubVariables['model_variable'] = Str::of($entity['model'])->camel();
        $stubVariables['modelPolicy'] = "{$entity['model']}Policy";

        return $stubVariables;
    }
}
