<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Database\Eloquent\Model;
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
        {--with-users : Include users based on the roles/permissions in seeder}
        {--include-passwords : Include actual hashed passwords from database}
        {--generate-passwords= : Generate passwords (<fg=green;options=bold>YourPassword</>|<fg=green;options=bold>random</>|<fg=green;options=bold>prompt</>)}
        {--all : Export all tenants/users regardless of roles/permissions}
        {--F|force : Override if the seeder already exists}
    ';

    protected ?string $generatedPassword = null;

    protected string $passwordMode = 'none';

    protected bool $useRandomPasswords = false;

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

        $withUsers = $this->option('with-users');
        $includePasswords = $this->option('include-passwords');
        $generatePasswordsValue = $this->option('generate-passwords');
        $all = $this->option('all');
        $option = $this->option('option');

        // Check if --generate-passwords was provided (has a non-null value)
        $generatePasswordsFlagPassed = $generatePasswordsValue !== null;

        if ($includePasswords && $generatePasswordsFlagPassed) {
            $this->components->error('Cannot use both --include-passwords and --generate-passwords. Choose one.');

            return self::INVALID;
        }

        // Handle password mode when --with-users or --all is used
        if (($withUsers || $all) && ! $this->resolvePasswordMode($includePasswords, $generatePasswordsFlagPassed, $generatePasswordsValue)) {
            return self::INVALID;
        }

        if (Utils::getRoleModel()::doesntExist() && Utils::getPermissionModel()::doesntExist()) {
            $this->components->warn('No roles or permissions found to export.');

            return self::INVALID;
        }

        $replacements = $this->collectReplacements($withUsers, $all, $option);

        $this->copySeederStubToApp(
            stub: 'ShieldSeeder',
            targetPath: $path,
            replacements: $replacements
        );

        $this->components->info('ShieldSeeder generated successfully.');
        $this->components->info('Now you can use it in your deploy script. i.e: <fg=yellow>php artisan db:seed --class=ShieldSeeder</>');

        return self::SUCCESS;
    }

    protected function resolvePasswordMode(bool $includePasswords, bool $generatePasswordsFlagPassed, ?string $generatePasswordsValue): bool
    {
        // --include-passwords takes precedence
        if ($includePasswords) {
            $this->passwordMode = 'include';
            $this->components->warn('Including hashed passwords. Handle generated seeder securely.');

            return true;
        }

        // --generate-passwords flag was passed
        if ($generatePasswordsFlagPassed) {
            return $this->handleGeneratePasswordsFlag($generatePasswordsValue);
        }

        // No password flag provided - prompt interactively
        return $this->promptForPasswordMode();
    }

    protected function handleGeneratePasswordsFlag(?string $value): bool
    {
        $this->passwordMode = 'generate';

        // --generate-passwords=random
        if ($value === 'random') {
            $this->useRandomPasswords = true;
            $this->components->info('Generating random passwords for users. Users will need to reset.');

            return true;
        }

        // --generate-passwords=prompt - interactive prompt
        if ($value === 'prompt') {
            return $this->promptForGeneratedPassword();
        }

        // --generate-passwords=SomePassword (custom password provided)
        if ($value !== null && $value !== '') {
            $this->generatedPassword = $value;
            $this->components->info('Using provided password for all users.');

            return true;
        }

        // Empty value - prompt for choice
        return $this->promptForGeneratedPassword();
    }

    protected function promptForPasswordMode(): bool
    {
        $choice = select(
            label: 'How would you like to handle user passwords?',
            options: [
                'none' => 'Skip passwords (users will need to reset)',
                'include' => 'Include existing hashed passwords from database',
                'generate' => 'Generate new passwords',
            ],
            default: 'none'
        );

        if ($choice === 'none') {
            $this->passwordMode = 'none';

            return true;
        }

        if ($choice === 'include') {
            $this->passwordMode = 'include';
            $this->components->warn('Including hashed passwords. Handle generated seeder securely.');

            return true;
        }

        // choice === 'generate'
        $this->passwordMode = 'generate';

        return $this->promptForGeneratedPassword();
    }

    protected function promptForGeneratedPassword(): bool
    {
        $choice = select(
            label: 'How would you like to generate passwords?',
            options: [
                'random' => 'Generate random passwords (users must reset)',
                'custom' => 'Use a custom password for all users',
            ],
            default: 'random'
        );

        if ($choice === 'random') {
            $this->useRandomPasswords = true;
            $this->components->info('Generating random passwords for users. Users will need to reset.');

            return true;
        }

        // choice === 'custom'
        $this->generatedPassword = password(
            label: 'Enter the password to use for all users',
            required: true,
        );
        $this->components->info('Using provided password for all users.');

        return true;
    }

    protected function collectReplacements(bool $withUsers, bool $all, ?string $option = null): array
    {
        $directPermissionNames = collect();
        $permissionsViaRoles = collect();
        $directPermissions = collect();
        $tenancyEnabled = Utils::isTenancyEnabled();

        if ((Utils::getRoleModel()::exists() && is_null($option)) || $option === 'permissions_via_roles') {
            $permissionsViaRoles = collect(Utils::getRoleModel()::with('permissions')->get())
                ->map(function (object $role) use ($directPermissionNames, $tenancyEnabled): array {
                    $rolePermissions = $role->permissions->pluck('name')->toArray();
                    $directPermissionNames->push($rolePermissions);

                    $data = [
                        'name' => $role->name,
                        'guard_name' => $role->guard_name,
                        'permissions' => $rolePermissions,
                    ];

                    if ($tenancyEnabled) {
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
            'TenancyEnabled' => $tenancyEnabled ? 'true' : 'false',
        ];

        // Auto-export tenants when tenancy is enabled (roles have tenant_id, so tenants must exist)
        if ($tenancyEnabled) {
            $tenants = $this->collectTenants($all, $withUsers);
            $replacements['Tenants'] = $tenants;
            $replacements['TenantPivotTable'] = $this->resolvePivotTableName();

            if ($tenants !== []) {
                $this->components->info(sprintf('Exporting %d tenants...', count($tenants)));
            }
        }

        // Export users when --with-users or --all is provided
        if ($withUsers || $all) {
            $users = $this->collectUsers($all, $option);
            $replacements['Users'] = $users;

            $this->components->info(sprintf('Exporting %d users...', count($users)));

            // Export user-tenant pivot when tenancy is enabled
            if ($tenancyEnabled) {
                $replacements['UserTenantPivot'] = $this->collectUserTenantPivot($all);
            }
        }

        return $replacements;
    }

    protected function collectTenants(bool $all, bool $withUsers): array
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

            // Get tenant IDs from roles
            $tenantIdsFromRoles = $roleModel::query()
                ->whereNotNull($teamForeignKey)
                ->distinct()
                ->pluck($teamForeignKey);

            // If exporting users, also include tenants from user-tenant pivot
            if ($withUsers) {
                $pivotTable = $this->resolvePivotTableName();
                if ($pivotTable && Schema::hasTable($pivotTable)) {
                    $tenantIdsFromUsers = DB::table($pivotTable)
                        ->distinct()
                        ->pluck($teamForeignKey);

                    $tenantIdsFromRoles = $tenantIdsFromRoles->merge($tenantIdsFromUsers)->unique();
                }
            }

            $query->whereIn('id', $tenantIdsFromRoles);
        }

        return $query->get()->map(fn (Model $tenant): mixed => $tenant->toArray())->toArray();
    }

    protected function collectUsers(bool $all, ?string $option = null): array
    {
        $userModel = Utils::getAuthProviderFQCN();
        $tenancyEnabled = Utils::isTenancyEnabled();

        // Get the morph class name (could be alias like 'user' or FQCN)
        $morphClass = (new $userModel)->getMorphClass();

        // Get user IDs from pivot tables directly (bypasses team scoping)
        $userIdsWithRoles = collect();
        $userIdsWithPermissions = collect();

        if (! $all) {
            if (is_null($option) || $option === 'permissions_via_roles') {
                $userIdsWithRoles = DB::table('model_has_roles')
                    ->where('model_type', $morphClass)
                    ->distinct()
                    ->pluck('model_id');
            }

            if (is_null($option) || $option === 'direct_permissions') {
                $userIdsWithPermissions = DB::table('model_has_permissions')
                    ->where('model_type', $morphClass)
                    ->distinct()
                    ->pluck('model_id');
            }

            $userIds = $userIdsWithRoles->merge($userIdsWithPermissions)->unique();
            $users = $userModel::whereIn('id', $userIds)->get();
        } else {
            $users = $userModel::all();
        }

        // Collect all tenant IDs to iterate through for role/permission collection
        $tenantIds = collect([null]); // null for global context
        if ($tenancyEnabled) {
            $teamForeignKey = Utils::getTenantModelForeignKey();
            $tenantIds = DB::table('roles')
                ->whereNotNull($teamForeignKey)
                ->distinct()
                ->pluck($teamForeignKey)
                ->prepend(null); // Include global context
        }

        return $users->map(function (Model $user) use ($option, $tenancyEnabled, $tenantIds) {
            $data = $user->toArray();

            // Handle password based on mode
            unset($data['password']);
            if ($this->passwordMode === 'include') {
                $data['password'] = $user->password;
            } elseif ($this->passwordMode === 'generate') {
                $password = $this->useRandomPasswords ? Str::random(16) : $this->generatedPassword;
                $data['password'] = bcrypt($password);
            }

            $collectRoles = is_null($option) || $option === 'permissions_via_roles';
            $collectPermissions = is_null($option) || $option === 'direct_permissions';

            if ($tenancyEnabled) {
                // Save current team context to restore after iteration
                $originalTeamId = getPermissionsTeamId();

                // Group roles/permissions by tenant
                $rolesByTenant = [];
                $permissionsByTenant = [];

                foreach ($tenantIds as $tenantId) {
                    setPermissionsTeamId($tenantId);
                    // Must unset relations to get fresh data for this tenant context
                    $user->unsetRelation('roles')->unsetRelation('permissions');

                    $tenantKey = $tenantId ?? '_global';

                    if ($collectRoles) {
                        $roles = $user->roles->pluck('name')->toArray();
                        if (! empty($roles)) {
                            $rolesByTenant[$tenantKey] = $roles;
                        }
                    }

                    if ($collectPermissions) {
                        $permissions = $user->permissions->pluck('name')->toArray();
                        if (! empty($permissions)) {
                            $permissionsByTenant[$tenantKey] = $permissions;
                        }
                    }
                }

                // Restore original team context
                setPermissionsTeamId($originalTeamId);

                if ($collectRoles) {
                    $data['tenant_roles'] = $rolesByTenant;
                }

                if ($collectPermissions) {
                    $data['tenant_permissions'] = $permissionsByTenant;
                }
            } else {
                if ($collectRoles) {
                    $data['roles'] = $user->roles->pluck('name')->toArray();
                }

                if ($collectPermissions) {
                    $data['permissions'] = $user->permissions->pluck('name')->toArray();
                }
            }

            return $data;
        })->toArray();
    }

    protected function collectUserTenantPivot(bool $all = false): array
    {
        $pivotTable = $this->resolvePivotTableName();
        if (! $pivotTable || ! Schema::hasTable($pivotTable)) {
            return [];
        }

        $columns = $this->getTenantPivotColumns();
        if ($columns === []) {
            return [];
        }

        $query = DB::table($pivotTable)->select($columns);

        // When not exporting all, filter to only users with roles/permissions
        if (! $all) {
            $userModel = Utils::getAuthProviderFQCN();
            // Get the morph class name (could be alias like 'user' or FQCN)
            $morphClass = (new $userModel)->getMorphClass();

            // Get user IDs directly from pivot tables (bypasses team scoping)
            $userIdsWithRoles = DB::table('model_has_roles')
                ->where('model_type', $morphClass)
                ->distinct()
                ->pluck('model_id');

            $userIdsWithPermissions = DB::table('model_has_permissions')
                ->where('model_type', $morphClass)
                ->distinct()
                ->pluck('model_id');

            $userIds = $userIdsWithRoles->merge($userIdsWithPermissions)->unique();

            $query->whereIn('user_id', $userIds);
        }

        return $query->get()
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
