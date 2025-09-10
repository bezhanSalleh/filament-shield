<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'shield:seeder', description: 'Create a seeder file from existing/configured roles and permission, that could be used within your deploy script.')]
class SeederCommand extends Command
{
    use CanManipulateFiles;
    use Prohibitable;

    /** @var string */
    public $signature = 'shield:seeder
        {--generate : Generates permissions for all entities as configured }
        {--option= : Generate only permissions via roles or direct permissions (<fg=green;options=bold>permissions_via_roles,direct_permissions</>)}
        {--F|force : Override if the seeder already exists }
    ';

    public function handle(): int
    {
        $path = database_path('seeders/ShieldSeeder.php');

        if (! $this->option('force') && $this->checkForCollision(paths: [$path])) {
            return self::INVALID;
        }

        if ($this->option('generate')) {
            $this->call('shield:generate', [
                '--all' => true,
            ]);
        }

        if (Utils::getRoleModel()::doesntExist() && Utils::getPermissionModel()::doesntExist()) {
            $this->components->warn('There are no roles or permissions to create the seeder. Please first run `shield:generate --all`');

            return self::INVALID;
        }

        $directPermissionNames = collect();
        $permissionsViaRoles = collect();
        $directPermissions = collect();

        $option = $this->option('option');

        if ((Utils::getRoleModel()::exists() && is_null($option)) || $option === 'permissions_via_roles') {
            $permissionsViaRoles = collect(Utils::getRoleModel()::with('permissions')->get())
                ->map(function (object $role) use ($directPermissionNames): array {
                    $rolePermissions = $role->permissions
                        ->pluck('name')
                        ->toArray();

                    $directPermissionNames->push($rolePermissions);

                    return [
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                        'permissions' => $rolePermissions,
                    ];
                });
        }

        if ((Utils::getPermissionModel()::exists() && is_null($option)) || $option === 'direct_permissions') {
            $directPermissions = collect(Utils::getPermissionModel()::get())
                ->filter(fn (object $permission): bool => ! in_array($permission->name, $directPermissionNames->unique()->flatten()->all()))
                ->map(fn (object $permission): array => [
                    'name' => $permission->name,
                    'guard_name' => $permission->guard_name,
                ]);
        }

        $this->copySeederStubToApp(
            stub: 'ShieldSeeder',
            targetPath: $path,
            replacements: [
                'RolePermissions' => $permissionsViaRoles->all(),
                'DirectPermissions' => $directPermissions->all(),
            ]
        );

        $this->components->info('ShieldSeeder generated successfully.');
        $this->components->info('Now you can use it in your deploy script. i.e: <fg=yellow>php artisan db:seed --class=ShieldSeeder</>');

        return self::SUCCESS;
    }
}
