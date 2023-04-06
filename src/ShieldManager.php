<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use Illuminate\Database\Eloquent\Model;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Facades\Shield;
use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;

class ShieldManager
{
    public static function make(): ShieldDriver
    {
        if (class_exists(static::getDriver())) {
            return new (static::getDriver())();
        }

        throw new \InvalidArgumentException('Invalid shield driver specified.');
    }

    protected static function getDriver(): string
    {
        return (string) str(Utils::getDriver())->ucfirst()->append('ShieldDriver')->toString();
    }

    public static function firstOrCreate(string $model, array $data): Model
    {
        $model = 'create' . ucfirst($model);
        return Shield::{$model}(static::prepareData($data));
    }

    protected static function prepareData(array $data): array
    {
        return match(Utils::getDriver()) {
            'spatie' => [
                'name' => $data['name'],
                'guard_name' => Utils::getFilamentAuthGuard()
            ],
            'bouncer' => [
                'name' => $data['name'],
                'title' => $data['title'],
            ],
            'custom' => $data,
            default => $data
        };
    }
}
