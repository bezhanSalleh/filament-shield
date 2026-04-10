<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Contracts\Auth\Authenticatable;
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
        $path = (new ReflectionClass($entity['modelFqcn']))->getFileName();

        if (Str::of($path)->contains(['vendor', 'src'])) {
            return Str::of($entity['model'])
                ->prepend(str(Utils::getPolicyPath())->append('\\'))
                ->replace('\\', DIRECTORY_SEPARATOR)
                ->append('Policy.php')
                ->toString();
        }

        $modelRelativePath = Str::of($path)->after('Models' . DIRECTORY_SEPARATOR);

        /** @phpstan-ignore-next-line */
        return Str::of(Utils::getPolicyPath())
            ->append(DIRECTORY_SEPARATOR)
            ->append($modelRelativePath)
            ->replaceLast('.php', 'Policy.php')
            ->toString();
    }

    protected function generatePolicyStubVariables(array $entity): array
    {
        $stubVariables = [];
        $policyConfig = Utils::getConfig()->policies;
        $singleParameterMethods = $policyConfig->single_parameter_methods ?? [];

        foreach (FilamentShield::getResourcePolicyActionsWithPermissions($entity['resourceFqcn']) as $method => $permission) {
            $stubVariables[$method] = [
                'stub' => resolve($entity['modelFqcn']) instanceof Authenticatable ? 'SingleParamMethod' : (in_array($method, $singleParameterMethods) ? 'SingleParamMethod' : 'MultiParamMethod'),
                'permission' => $permission,
            ];
        }

        $stubVariables['auth_model_fqcn'] = 'Illuminate\\Foundation\\Auth\\User as AuthUser';
        $stubVariables['auth_model_name'] = 'AuthUser';
        $stubVariables['auth_model_variable'] = 'authUser';

        $reflectionClass = new ReflectionClass($entity['modelFqcn']);
        $namespace = $reflectionClass->getNamespaceName();
        $path = $reflectionClass->getFileName();

        $policyBaseNamespace = Utils::resolveNamespaceFromPath(Utils::getPolicyPath());

        $stubVariables['namespace'] = Str::of($path)->contains(['vendor', 'src'])
            ? $policyBaseNamespace
            : $policyBaseNamespace . Str::of($namespace)->after('Models')->toString(); /** @phpstan-ignore-line */
        $stubVariables['model_name'] = $entity['model'];
        $stubVariables['model_fqcn'] = $namespace . '\\' . $entity['model'];
        $stubVariables['model_variable'] = Str::of($entity['model'])->camel();
        $stubVariables['modelPolicy'] = $entity['model'] . 'Policy';

        return $stubVariables;
    }
}
