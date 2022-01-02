<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Illuminate\Console\Command;

class FilamentShieldCommand extends Command
{
    public $signature = 'filament-shield';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
