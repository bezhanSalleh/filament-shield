<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;

trait CanManipulateFiles
{
    protected function checkForCollision(array $paths): bool
    {
        foreach ($paths as $path) {
            if ($this->fileExists($path)) {
                $this->components->error("$path already exists, aborting.");

                return true;
            }
        }

        return false;
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = app(Filesystem::class);

        // Determine the stub path
        if (! $this->fileExists($stubPath = base_path("stubs/filament-shield/{$stub}.stub"))) {
            $stubPath = $this->getDefaultStubPath() . "/{$stub}.stub";
        }

        // Read the stub content
        $stubContent = Str::of($filesystem->get($stubPath));

        // Replace RolePermissions and DirectPermissions placeholders
        foreach ($replacements as $placeholder => $replacement) {
            if (in_array($placeholder, ['RolePermissions', 'DirectPermissions'])) {
                $stubContent = $stubContent->replace('{{ ' . $placeholder . ' }}', $replacement);
            }
        }

        // Handle methods placeholder (for policy stubs)
        $methods = '';
        foreach ($replacements as $methodName => $replacement) {
            if (is_array($replacement) && isset($replacement['stub'], $replacement['permission'])) {
                if (! $this->fileExists($methodStubPath = base_path("stubs/filament-shield/{$stub}.stub"))) {
                    $methodStubPath = $this->getDefaultStubPath() . "/{$replacement['stub']}.stub";
                }
                $methodStub = Str::of($filesystem->get($methodStubPath));
                $methodContent = $methodStub->replace(
                    ['{{ methodName }}', '{{ auth_model_name }}', '{{ auth_model_variable }}', '{{ model_name }}', '{{ model_variable }}', '{{ permission }}'],
                    [$methodName, $replacements['auth_model_name'], $replacements['auth_model_variable'], $replacements['model_name'], $replacements['model_variable'], $replacement['permission']]
                );
                $methods .= $methodContent . "\n";
            }
        }
        $stubContent = $stubContent->replace('{{ methods }}', $methods);

        // Replace other placeholders (namespace, auth_model_fqcn, etc.)
        $namespace = $replacements['namespace'] ?? '';
        $authModelFqcn = $replacements['auth_model_fqcn'] ?? '';
        $modelFqcn = $replacements['model_fqcn'] ?? '';
        $modelPolicy = $replacements['modelPolicy'] ?? '';

        $stubContent = $stubContent->replace(
            ['{{ namespace }}', '{{ auth_model_fqcn }}', '{{ model_fqcn }}', '{{ modelPolicy }}'],
            [$namespace, $authModelFqcn, $modelFqcn, $modelPolicy]
        );

        // Convert Stringable to string before writing
        $this->writeFile($targetPath, (string) $stubContent);
    }

    protected function fileExists(string $path): bool
    {
        $filesystem = new Filesystem;

        return $filesystem->exists($path);
    }

    protected function writeFile(string $path, string $contents): void
    {
        $filesystem = new Filesystem;

        $filesystem->ensureDirectoryExists(
            (string) Str::of($path)
                ->beforeLast(DIRECTORY_SEPARATOR),
        );

        $filesystem->put($path, $contents);
    }

    protected function replaceInFile(string $file, string $search, string $replace): void
    {
        file_put_contents(
            $file,
            str_replace($search, $replace, file_get_contents($file))
        );
    }

    protected function copy(string $source, string $destination): bool
    {
        $filesystem = new Filesystem;

        if (! $this->fileExists($destination)) {
            $filesystem->copy($source, $destination);
            $this->components->info("$destination file published!");

            return true;
        }

        $this->components->warn("$destination already exists, skipping ...");

        return false;
    }

    protected function getDefaultStubPath(): string
    {
        $reflectionClass = new ReflectionClass($this);

        return (string) str($reflectionClass->getFileName())
            ->beforeLast('src')
            ->append('stubs');
    }
}
