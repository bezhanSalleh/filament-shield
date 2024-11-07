<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

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
        $filesystem = new Filesystem;

        if (! $this->fileExists($stubPath = base_path("stubs/filament-shield/{$stub}.stub"))) {
            $stubPath = __DIR__ . "/../../../stubs/{$stub}.stub";
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
}
