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
        $modelClass = $entity['fqcn']::getModel();
        $reflection = new \ReflectionClass($modelClass);
        $modelPath = $reflection->getFileName();

        $policyClassName = class_basename($modelClass) . 'Policy';

        $relativePolicyPath = $policyClassName . '.php';

        // 🔍 Check all configured policy paths
        foreach (Utils::getPolicyPaths() as $basePolicyPath) {
            $fullPath = base_path(trim($basePolicyPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $relativePolicyPath);
            if (file_exists($fullPath)) {
                return $fullPath;
            }
        }

        // 👀 Dynamically find the package root and try inferring policy location
        if (Str::of($modelPath)->contains(['vendor', 'src'])) {
            $segments = explode(DIRECTORY_SEPARATOR, $modelPath);
            $srcIndex = array_search('src', $segments);
            if ($srcIndex !== false) {
                $packageBaseDir = implode(DIRECTORY_SEPARATOR, array_slice($segments, 0, $srcIndex + 1));
                $packagePolicyPath = $packageBaseDir . DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . $relativePolicyPath;

                if (file_exists($packagePolicyPath)) {
                    return $packagePolicyPath;
                }
            }
        }

        // 🛑 Final fallback
        return app_path('Policies' . DIRECTORY_SEPARATOR . $relativePolicyPath);
    }

    protected function generatePolicyStubVariables(array $entity): array
    {
        $stubVariables = collect(Utils::getResourcePermissionPrefixes($entity['fqcn']))
            ->reduce(function ($gates, $permission) use ($entity) {
                $gates[Str::studly($permission)] = $permission . '_' . $entity['resource'];

                return $gates;
            }, []);

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
