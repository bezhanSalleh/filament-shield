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

        if (! $this->fileExists($stubPath = base_path("stubs/filament-shield/{$stub}.stub"))) {
            $stubPath = $this->getDefaultStubPath() . "/{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

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

        // Replace methods placeholder with generated methods
        $stub = $stub->replace('{{ methods }}', $methods);

        // Replace other placeholders in the policy class
        $stub = (string) $stub->replace(
            ['{{ namespace }}', '{{ auth_model_fqcn }}', '{{ model_fqcn }}', '{{ modelPolicy }}'],
            [$replacements['namespace'], $replacements['auth_model_fqcn'], $replacements['model_fqcn'], $replacements['modelPolicy']]
        );

        $this->writeFile($targetPath, $stub);
    }

    protected function copySeederStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = new Filesystem;

        if (! $this->fileExists($stubPath = base_path('stubs' . DIRECTORY_SEPARATOR . 'filament-shield' . DIRECTORY_SEPARATOR . "{$stub}.stub"))) {
            $stubPath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . "{$stub}.stub";
        }

        $stub = Str::of($filesystem->get($stubPath));

        foreach ($replacements as $key => $replacement) {
            $stub = $stub->replace("{{ {$key} }}", is_array($replacement) ? json_encode($replacement) : $replacement);
        }

        $stub = (string) $stub;

        $this->writeFile($targetPath, $stub);
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
