<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Throwable;

class MakeShieldUpgradeCommand extends Command
{
    public $signature = 'shield:upgrade';

    public $description = 'Upgrade shield';

    public function handle(): int
    {
        try {
            $path = glob(database_path('migrations/*_filament_shield_settings_table.php'));

            if (! blank($path) && File::exists($path[0])) {
                File::delete($path);
            }

            if (File::exists($seeder = database_path('seeders/ShieldSettingSeeder.php'))) {
                File::delete($seeder);
            }

            Schema::disableForeignKeyConstraints();

            DB::table('migrations')->where('migration', 'like', '%_filament_shield_settings_%')->delete();

            DB::statement('DROP TABLE IF EXISTS filament_shield_settings');

            Schema::enableForeignKeyConstraints();
        } catch (Throwable $e) {
            $this->components->info($e);

            return self::FAILURE;
        }

        $this->components->info('Filament Shield upgraded.');

        return self::SUCCESS;
    }
}
