<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use Illuminate\Filesystem\Filesystem;

trait CanBackupAFile
{
    protected function isBackupPossible($sourcePath, $distinationPath): bool
    {
        if ($this->fileAlreadyExists($sourcePath)) {
            $this->backup($sourcePath, $distinationPath);

            return true;
        }

        return false;
    }

    protected function fileAlreadyExists(string $path): bool
    {
        return (new Filesystem())->exists($path);
    }

    protected function backup(string $sourcePath, string $distinationPath): void
    {
        (new Filesystem())->copy($sourcePath, $distinationPath);
    }
}
