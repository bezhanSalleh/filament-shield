<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MakeUpgradeShieldCommand extends Command
{
    use Concerns\CanBackupAFile;

    public $signature = 'shield:upgrade';

    public $description = 'Upgrade Filament Shield.';

    public function handle(): int
    {
        $confirm = $this->confirm('This command will override Shield\'s config file, translation files and Resource?', false);
        if ($confirm || $this->option('no-interaction')) {
            (new Filesystem())->ensureDirectoryExists(config_path());

            if ($this->isBackupPossible(config_path('filament-shield.php'), config_path('filament-shield.php.bak'))) {
                $this->info('Config backup created.');
            }

            (new Filesystem())->copy(__DIR__.'/../../config/filament-shield.php', config_path('filament-shield.php'));

            (new Filesystem())->ensureDirectoryExists(lang_path());
            (new Filesystem())->copyDirectory(__DIR__.'/../../resources/lang', lang_path('/vendor/filament-shield'));

            (new Filesystem())->ensureDirectoryExists(lang_path());
            (new Filesystem())->copyDirectory(__DIR__.'/../../resources/views', resource_path('/views/vendor/filament-shield'));

            $this->call('shield:publish');

            $this->info('Published Shields\' config, translations, views & Resource.');

            if (config('filament-shield.exclude.enabled')) {
                Artisan::call('shield:generate --exclude');
                $this->info(Artisan::output());
            } else {
                Artisan::call('shield:generate');
            }

            $this->info('(re)Discovered and (re)Generated all permissions and policies.');

            return self::SUCCESS;
        }
        $this->error('shield:upgrade command aborted!');

        return self::INVALID;
    }
}
