<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;
use ReflectionClass;
use RuntimeException;

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

        /** @phpstan-ignore-next-line */
        return Str::of($path)
            ->replace('Models', Str::of($this->resolveNamespaceFromPath(Utils::getPolicyPath()))->afterLast('\\')->toString())
            ->replaceLast('.php', 'Policy.php')
            ->replace('\\', DIRECTORY_SEPARATOR)
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

        $policyNamespace = Str::of($this->resolveNamespaceFromPath(Utils::getPolicyPath()))->afterLast('\\')->toString();

        $stubVariables['namespace'] = Str::of($path)->contains(['vendor', 'src'])
            ? $this->resolveNamespaceFromPath(Utils::getPolicyPath())
            : Str::of($namespace)->replace('Models', $policyNamespace)->toString(); /** @phpstan-ignore-line */
        $stubVariables['model_name'] = $entity['model'];
        $stubVariables['model_fqcn'] = $namespace . '\\' . $entity['model'];
        $stubVariables['model_variable'] = Str::of($entity['model'])->camel();
        $stubVariables['modelPolicy'] = "{$entity['model']}Policy";

        return $stubVariables;
    }

    protected function resolveNamespaceFromPath(string $configuredPath): string
    {
        // Normalize separators
        $configuredPath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $configuredPath);

        // Only prepend base_path if it's relative
        if (in_array(preg_match('/^[a-zA-Z]:' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', $configuredPath), [0, false], true)
            && ! Str::startsWith($configuredPath, DIRECTORY_SEPARATOR)) {
            $configuredPath = base_path($configuredPath);
        }

        $composer = json_decode(file_get_contents(base_path('composer.json')), true);
        $psr4 = $composer['autoload']['psr-4'] ?? [];

        foreach ($psr4 as $namespace => $base) {
            $basePath = base_path(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, $base));
            $basePath = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
            $checkPath = rtrim($configuredPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

            if (strtolower($checkPath) === strtolower($basePath) || Str::startsWith(strtolower($checkPath), strtolower($basePath))) {
                $relative = Str::after($checkPath, $basePath);
                $relative = rtrim($relative, DIRECTORY_SEPARATOR);

                $ns = rtrim((string) $namespace, '\\');
                if ($relative !== '' && $relative !== '0') {
                    $ns .= '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $relative);
                }

                return $ns;
            }
        }

        throw new RuntimeException("Configured path does not match any PSR-4 mapping: {$configuredPath}");
    }
}
