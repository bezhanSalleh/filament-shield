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

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = $this->confirm('Shield Resource already exists. Overwrite?', false);
            if (! $confirmed) {
                return self::INVALID;
            }
        }

        (new Filesystem())->ensureDirectoryExists($baseResourcePath);
        (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources', $baseResourcePath);

        $this->info('Shield\'s Resource have been published successfully!');

        return self::SUCCESS;
    }
}
