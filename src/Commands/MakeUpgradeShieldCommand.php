<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeUpgradeShieldCommand extends Command
{
    public $signature = 'shield:upgrade';

    public $description = 'Upgrade Filament Shield.';

    public function handle(): int
    {
        $confirm = $this->confirm('This command will override Shield\'s config file, translation files and Resource?', false);
        if ($confirm || $this->option('no-interaction')) {

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-config',
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-views'
            ]);

            $this->call('vendor:publish', [
                '--tag' => 'filament-shield-translations',
            ]);

            $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield')->replace('\\', '/'), );
            (new Filesystem())->ensureDirectoryExists($baseResourcePath);
            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources', $baseResourcePath);

            $this->info('Published Shield\'s Resource.');

            $this->call('shield:generate');
            $this->info('(re)Discovered and (re)Generated all permissions and policies.');

            return self::SUCCESS;
        }
        $this->error('shield:upgrade command aborted!');

        return self::INVALID;
    }
}
