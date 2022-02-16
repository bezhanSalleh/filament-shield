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
            (new Filesystem())->ensureDirectoryExists(config_path());
            (new Filesystem())->copy(__DIR__.'/../../config/filament-shield.php', config_path('filament-shield.php'));
            $this->info('Published Config file.');

            (new Filesystem())->ensureDirectoryExists(lang_path());
            (new Filesystem())->copyDirectory(__DIR__.'/../../resources/lang', lang_path('/vendor/filament-shield'));
            $this->info('Publishd Translation files.');

            $baseResourcePath = app_path((string) Str::of('Filament\\Resources\\Shield')->replace('\\', '/'), );
            (new Filesystem())->ensureDirectoryExists($baseResourcePath);
            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/resources', $baseResourcePath);

            $basePagePath = app_path((string) Str::of('Filament\\Pages\\Shield')->replace('\\', '/'), );
            (new Filesystem())->ensureDirectoryExists($basePagePath);
            (new Filesystem())->copyDirectory(__DIR__.'/../../stubs/pages', $basePagePath);

            $this->info('Published Shield\'s Resource & Page.');

            Artisan::call('shield:generate');
            $this->info('(re)Discovered and (re)Generated all permissions and policies.');

            return self::SUCCESS;
        }
        $this->error('shield:upgrade command aborted!');

        return self::INVALID;
    }
}
