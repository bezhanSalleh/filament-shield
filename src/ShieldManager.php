<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield;

use BezhanSalleh\FilamentShield\Contracts\ShieldDriver;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Eloquent\Model;

class ShieldManager
{
    public static function make(): ShieldDriver
    {
        if (class_exists(static::getDriver())) {
            return new (static::getDriver())();
        }
        ray(static::getDriver());
        throw new \InvalidArgumentException('Invalid shield driver specified.');
    }

    protected static function getDriver(): string
    {
        return (string) str(Utils::getDriver())
            ->ucfirst()
            ->append('ShieldDriver')
            ->prepend('App\\Filament\\Resources\\Shield\\')
            ->toString();
    }

    public static function firstOrCreate(string $model, array $data): Model
    {
        $model = 'create'.ucfirst($model);

        return static::make()->{$model}(static::prepareData($data));
    }

    protected static function prepareData(array $data): array
    {
        return match (Utils::getDriver()) {
            'spatie' => [
                'name' => $data['name'],
                'guard_name' => Utils::getFilamentAuthGuard(),
            ],
            'bouncer' => [
                'name' => $data['name'],
                'title' => $data['title'] ?? str($data['name'])->headline()->toString(),
            ],
            'custom' => $data,
            default => $data
        };
    }

    public static function sync(string $model, $user, string|array $abilities): void
    {
        $sync = 'sync'.ucfirst($model);
        static::make()->{$sync}($user, $abilities);
    }

    public static function has(string $model, $user, string $ability): bool
    {
        $has = 'has'.ucfirst($model);

        return static::make()->{$has}($user, $ability);
    }

    public static function giveRolePermissions(Model $role, mixed $abilities): void
    {
        static::make()->givePermissionsToRole($role, $abilities);
    }
}
