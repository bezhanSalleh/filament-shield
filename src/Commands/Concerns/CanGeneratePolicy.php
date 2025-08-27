<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Support\Str;
use ReflectionClass;

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
            ->replace('/', '\\')
            ->toString();
    }

    protected function generatePolicyPath(array $entity): string
    {
        $path = (new ReflectionClass($entity['fqcn']::getModel()))->getFileName();

        if (Str::of($path)->contains(['vendor', 'src'])) {
            $basePolicyPath = app_path(
                (string) Str::of($entity['model'])
                    ->prepend(str(Utils::getPolicyPath())->append('\\'))
                    ->replace('\\', DIRECTORY_SEPARATOR)
                    ->toString(),
            );

            return "{$basePolicyPath}Policy.php";
        }

        /** @phpstan-ignore-next-line */
        $basePath = Str::of($path)
            ->replace('Models', Utils::getPolicyPath())
            ->replaceLast('.php', 'Policy.php')
            ->replace('\\', DIRECTORY_SEPARATOR)
            ->toString();

        return $basePath;
    }

    protected function generatePolicyStubVariables(array $entity): array
    {
        $stubVariables = collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->reduce(function (array $gates, string $permission) use ($entity) {
                $gates[Str::studly($permission)] = $permission . '_' . $entity['resource'];

                return $gates;
            }, []);

        $stubVariables['auth_model_fqcn'] = 'Illuminate\\Foundation\\Auth\\User as AuthUser';
        $stubVariables['auth_model_name'] = 'AuthUser';
        $stubVariables['auth_model_variable'] = 'authUser';

        $reflectionClass = new ReflectionClass($entity['fqcn']::getModel());
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
