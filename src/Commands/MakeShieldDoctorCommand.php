<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;

class MakeShieldDoctorCommand extends Command
{
    public $signature = 'shield:doctor';

    public $description = 'Create Permissions and/or Policy for the given Filament Resource Model';

    public function handle(): int
    {
        $this->call('about', [
            '--only' => 'filament_shield',
        ]);

        return self::SUCCESS;
    }
}
