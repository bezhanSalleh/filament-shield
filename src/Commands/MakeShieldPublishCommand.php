<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeShieldPublishCommand extends Command
{
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:publish';

    public $description = 'Publish filament shield\'s Resource.';

    public function handle(): int
    {
        $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield')->replace('\\', '/'));
        $roleResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield\\RoleResource.php')->replace('\\', '/'));

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = $this->confirm('Shield Resource already exists. Overwrite?', true);
            if (! $confirmed) {
                return self::INVALID;
            }
        }

        (new Filesystem())->ensureDirectoryExists($baseResourcePath);
        (new Filesystem())->copyDirectory(__DIR__.'/../Resources', $baseResourcePath);

        $currentNamespace = 'BezhanSalleh\\FilamentShield\\Resources';
        $newNamespace = 'App\\Filament\\Resources\\Shield';

        $this->replaceInFile($roleResourcePath, $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath.'/RoleResource/Pages/CreateRole.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath.'/RoleResource/Pages/EditRole.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath.'/RoleResource/Pages/ViewRole.php', $currentNamespace, $newNamespace);
        $this->replaceInFile($baseResourcePath.'/RoleResource/Pages/ListRoles.php', $currentNamespace, $newNamespace);

        $this->info('Shield\'s Resource have been published successfully!');

        return self::SUCCESS;
    }
}
