<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;
use BezhanSalleh\FilamentShield\Support\Utils;

class ShieldManager
{
    public function make(): ShieldDriver
    {
        if (class_exists(static::getDriver())) {
            return new (static::getDriver())();
        }

        throw new \InvalidArgumentException('Invalid shield driver specified.');
    }

    protected static function getDriver(): string
    {
        return (string) str(Utils::getDriver())->ucfirst()->append('Driver')->toString();
    }
}
