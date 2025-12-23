<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use stdClass;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;

#[AsCommand(name: 'shield:seeder', description: 'Create a seeder file from existing/configured roles and permissions, with optional tenant and user support.')]
class SeederCommand extends Command
{
    use CanManipulateFiles;
    use Prohibitable;

    /** @var string */
    public $signature = 'shield:seeder
        {--generate : Generates permissions for all entities as configured}
        {--option= : Generate only permissions via roles or direct permissions (<fg=green;options=bold>permissions_via_roles,direct_permissions</>)}
        {--with-tenants : Include tenants in seeder when tenancy enabled}
        {--with-users : Include users based on the roles/permissions in seeder}
        {--include-passwords : Include actual hashed passwords from database}
        {--generate-passwords : Generate passwords (prompts for custom or random)}
        {--all : Export all tenants/users regardless of roles/permissions}
        {--F|force : Override if the seeder already exists}
    ';

    protected ?string $customPassword = null;

    protected string $passwordMode = 'none';

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

        $withTenants = $this->option('with-tenants');
        $withUsers = $this->option('with-users');
        $includePasswords = $this->option('include-passwords');
        $generatePasswords = $this->option('generate-passwords');
        $all = $this->option('all');
        $option = $this->option('option');

        if ($withTenants && ! Utils::isTenancyEnabled()) {
            $this->components->warn('Tenancy is not enabled.');

            return self::FAILURE;
        }

        if ($includePasswords && $generatePasswords) {
            $this->components->error('Cannot use both --include-passwords and --generate-passwords. Choose one.');

            return self::INVALID;
        }

        if ($includePasswords) {
            $this->passwordMode = 'include';
            $this->components->warn('Including hashed passwords. Handle generated seeder securely.');
        }

        if ($generatePasswords) {
            $this->passwordMode = 'generate';
            $this->customPassword = $this->resolveGeneratedPassword();

            if ($this->customPassword) {
                $this->components->info('Using provided password for all users.');
            } else {
                $this->components->info('Generating random passwords for users. Users will need to reset.');
            }
        }

        if (Utils::getRoleModel()::doesntExist() && Utils::getPermissionModel()::doesntExist()) {
            $this->components->warn('No roles or permissions found to export.');

            return self::INVALID;
        }

        $replacements = $this->collectReplacements($withTenants, $withUsers, $all, $option);

        $this->copySeederStubToApp(
            stub: 'ShieldSeeder',
            targetPath: $path,
            replacements: $replacements
        );

        $this->components->info('ShieldSeeder generated successfully.');
        $this->components->info('Now you can use it in your deploy script. i.e: <fg=yellow>php artisan db:seed --class=ShieldSeeder</>');

        return self::SUCCESS;
    }

    protected function resolveGeneratedPassword(): ?string
    {
        $choice = select(
            label: 'How would you like to handle user passwords?',
            options: [
                'random' => 'Generate random passwords (users must reset)',
                'custom' => 'Use a custom password for all users',
            ],
            default: 'random'
        );

        if ($choice === 'custom') {
            return password(
                label: 'Enter the password to use for all users',
                required: true,
            );
        }

        return null;
    }

    protected function collectReplacements(bool $withTenants, bool $withUsers, bool $all, ?string $option = null): array
    {
        $directPermissionNames = collect();
        $permissionsViaRoles = collect();
        $directPermissions = collect();

        if ((Utils::getRoleModel()::exists() && is_null($option)) || $option === 'permissions_via_roles') {
            $permissionsViaRoles = collect(Utils::getRoleModel()::with('permissions')->get())
                ->map(function (object $role) use ($directPermissionNames): array {
                    $rolePermissions = $role->permissions->pluck('name')->toArray();
                    $directPermissionNames->push($rolePermissions);

                    $data = [
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                        'permissions' => $rolePermissions,
                    ];

                    if (Utils::isTenancyEnabled()) {
                        $teamForeignKey = Utils::getTenantModelForeignKey();
                        $data[$teamForeignKey] = $role->{$teamForeignKey};
                    }

                    return $data;
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

        $replacements = [
            'RolePermissions' => $permissionsViaRoles->all(),
            'DirectPermissions' => $directPermissions->all(),
            'Tenants' => [],
            'Users' => [],
            'UserTenantPivot' => [],
            'TenantModel' => Utils::getTenantModel() ?? '',
            'UserModel' => Utils::getAuthProviderFQCN(),
            'TenantPivotTable' => '',
            'TenantForeignKey' => Utils::getTenantModelForeignKey(),
            'TenancyEnabled' => Utils::isTenancyEnabled() ? 'true' : 'false',
        ];

        if ($withTenants && Utils::isTenancyEnabled()) {
            $tenants = $this->collectTenants($all);
            $replacements['Tenants'] = $tenants;
            $replacements['TenantPivotTable'] = $this->resolvePivotTableName();

            $this->components->info(sprintf('Exporting %d tenants...', count($tenants)));
        }

        if ($withUsers) {
            $users = $this->collectUsers($all, $option);
            $replacements['Users'] = $users;

            $this->components->info(sprintf('Exporting %d users...', count($users)));

            if ($withTenants && Utils::isTenancyEnabled()) {
                $replacements['UserTenantPivot'] = $this->collectUserTenantPivot();
            }
        }

        return $replacements;
    }

    protected function collectTenants(bool $all): array
    {
        /**
         * @var string|null $tenantModel
         */
        $tenantModel = Utils::getTenantModel();

        if (! $tenantModel) {
            return [];
        }

        $query = $tenantModel::query();

        if (! $all) {
            $teamForeignKey = Utils::getTenantModelForeignKey();
            $roleModel = Utils::getRoleModel();
            $tenantIds = $roleModel::query()
                ->whereNotNull($teamForeignKey)
                ->distinct()
                ->pluck($teamForeignKey);
            $query->whereIn('id', $tenantIds);
        }

        return $query->get()->map(fn (Model $tenant): mixed => $tenant->toArray())->toArray();
    }

    protected function collectUsers(bool $all, ?string $option = null): array
    {
        $userModel = Utils::getAuthProviderFQCN();
        $query = $userModel::query()->with(['roles', 'permissions']);

        if (! $all) {
            $query->where(function (Builder | Relation $q) use ($option): void {
                if ($option === 'permissions_via_roles') {
                    $q->whereHas('roles');
                } elseif ($option === 'direct_permissions') {
                    $q->whereHas('permissions');
                } else {
                    $q->whereHas('roles')->orWhereHas('permissions');
                }
            });
        }

        return $query->get()->map(function (Model $user) use ($option) {
            $data = $user->only(['name', 'email', 'type']);

            if ($this->passwordMode === 'include') {
                $data['password'] = $user->password;
            } elseif ($this->passwordMode === 'generate') {
                $data['password'] = bcrypt($this->customPassword ?? Str::random(16));
            }

            if (is_null($option) || $option === 'permissions_via_roles') {
                $data['roles'] = $user->roles->pluck('name')->toArray();
            }

            if (is_null($option) || $option === 'direct_permissions') {
                $data['permissions'] = $user->permissions->pluck('name')->toArray();
            }

            return $data;
        })->toArray();
    }

    protected function collectUserTenantPivot(): array
    {
        $pivotTable = $this->resolvePivotTableName();
        if (! $pivotTable || ! Schema::hasTable($pivotTable)) {
            return [];
        }

        $columns = $this->getTenantPivotColumns();
        if ($columns === []) {
            return [];
        }

        return DB::table($pivotTable)
            ->select($columns)
            ->get()
            ->map(fn (stdClass $row): array => (array) $row)
            ->toArray();
    }

    protected function getTenantPivotColumns(): array
    {
        $pivotTable = $this->resolvePivotTableName();
        if (! $pivotTable || ! Schema::hasTable($pivotTable)) {
            return [];
        }

        $columns = Schema::getColumnListing($pivotTable);

        return array_values(array_diff($columns, ['created_at', 'updated_at']));
    }

    protected function resolvePivotTableName(): ?string
    {
        $tenantModel = Utils::getTenantModel();
        if (! $tenantModel) {
            return null;
        }

        $userModel = Utils::getAuthProviderFQCN();
        $user = new $userModel;

        if (method_exists($user, 'organizations')) {
            $relation = $user->organizations();

            return $relation->getTable();
        }

        if (method_exists($user, 'tenants')) {
            $relation = $user->tenants();

            return $relation->getTable();
        }

        $tenantTable = (new $tenantModel)->getTable();
        $userTable = $user->getTable();

        $tables = [$tenantTable, $userTable];
        sort($tables);

        return implode('_', array_map(Str::singular(...), $tables));
    }
}
