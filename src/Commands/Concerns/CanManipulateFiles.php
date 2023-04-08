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
                $this->components->error("$path already exists, waiting for user confirmation ...");
                if ($this->components->confirm('Do you want to overwrite the existing file?')) {
                    return false;
                }

                return true;
            }
        }

        return false;
    }

    protected function copyStubToApp(string $stub, string $targetPath, array $replacements = []): void
    {
        $filesystem = new Filesystem();

        if (! $this->fileExists($stubPath = base_path("stubs/filament-shield/{$stub}.stub"))) {
            $stubPath = __DIR__."/../../../stubs/{$stub}.stub";
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
        $filesystem = new Filesystem();

        return $filesystem->exists($path);
    }

    protected function writeFile(string $path, string $contents): void
    {
        $filesystem = new Filesystem();

        $filesystem->ensureDirectoryExists(
            (string) Str::of($path)
                ->beforeLast(DIRECTORY_SEPARATOR),
        );

        $filesystem->put($path, $contents);
    }

    protected function replaceInFile(string $search, string $replace, string $path): void
    {
        $fileContent = file($path, FILE_IGNORE_NEW_LINES);
        $newContent = [];

        $found = false;
        foreach ($fileContent as $line) {
            if (! $found && strpos($line, $search) !== false) {
                if (filled($replace)) {
                    $line = str_replace($search, $replace, $line);
                    $newContent[] = $line;
                }
                $found = true;
            } else {
                $newContent[] = $line;
            }
        }

        file_put_contents($path, implode(PHP_EOL, $newContent));
        // $content = file_get_contents($path);
        // $position = strpos($content, $search);

        // if ($position !== false) {
        //     file_put_contents(
        //         $path,
        //         substr_replace($content, $replace, $position, strlen($search))
        //     );
        // }
    }

    protected function existsInFile(string $search, string $path): bool
    {
        return Str::contains(file_get_contents($path), $search);
    }
}
