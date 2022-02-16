<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakePublishShieldCommand extends Command
{
    use Concerns\CanManipulateFiles;

    public $signature = 'shield:publish';

    public $description = 'Publish filament shield\'s Resource.';

    public function handle(): int
    {
        $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield')->replace('\\', '/'), );
        $roleResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield\\RoleResource.php')->replace('\\', '/'), );

        // $basePagePath = app_path((string) Str::of('Filament\\Pages\\Shield')->replace('\\', '/'), );
        // $configPagePath = app_path((string) Str::of('Filament\\Pages\\Shield\\Configuration.php')->replace('\\', '/'), );

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = $this->confirm('Shield Resource already exists. Overwrite?', true);
            if (! $confirmed) {
                return self::INVALID;
            }
        }

        (new Filesystem())->ensureDirectoryExists($baseResourcePath);
        (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources', $baseResourcePath);
        // (new Filesystem())->ensureDirectoryExists($basePagePath);
        // (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/pages', $basePagePath);

        $this->info('Shield\'s Resource have been published successfully!');

        return self::SUCCESS;
    }
}
