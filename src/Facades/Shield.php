<?php

namespace BezhanSalleh\FilamentShield\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static ShieldDriver make()
 * @method static void createRole(array $data)
 * @method static void createPermission(array $data)
 *
 * @see \BezhanSalleh\FilamentShield\ShieldManager
 */
class Shield extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'shield';
    }
}
