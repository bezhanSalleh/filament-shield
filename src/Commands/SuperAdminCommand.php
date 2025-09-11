<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'shield:super-admin', description: 'Assign the super admin role to a user')]
class SuperAdminCommand extends Command
{
    use Prohibitable;

    public $signature = 'shield:super-admin
        {--user= : ID of user to be made super admin.}
        {--panel= : Panel ID to get the configuration from.}
        {--tenant= : Team/Tenant ID to assign role to user.}
    ';

    protected Authenticatable $superAdmin;

    protected ?string $panel = null;

    protected ?\Illuminate\Database\Eloquent\Model $superAdminRole = null;

    protected function getAuthGuard(): Guard
    {
        return Filament::getPanel($this->panel)->auth();
    }

    protected function getUserProvider(): UserProvider
    {
        return $this->getAuthGuard()->getProvider();
    }

    protected function getUserModel(): string
    {
        /** @var EloquentUserProvider $provider */
        $provider = $this->getUserProvider();

        return $provider->getModel();
    }

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $this->panel = $this->option('panel') ?? select(
            label: 'Which Panel would you like to use?',
            options: collect(Filament::getPanels())->keys(),
            required: true
        );

        $usersCount = static::getUserModel()::count();
        $tenantId = $this->option('tenant');

        if ($this->option('user')) {
            $this->superAdmin = static::getUserModel()::findOrFail($this->option('user'));
        } elseif ($usersCount === 1) {
            $this->superAdmin = static::getUserModel()::first();
        } elseif ($usersCount > 1) {
            $this->table(
                ['ID', 'Name', 'Email', 'Roles'],
                static::getUserModel()::with('roles')->get()->map(fn (Authenticatable $user): array => [
                    'id' => $user->getKey(),
                    'name' => $user->getAttribute('name'),
                    'email' => $user->getAttribute('email'),
                    /** @phpstan-ignore-next-line */
                    'roles' => implode(',', $user->roles->pluck('name')->toArray()),
                ])
            );

            $superAdminId = text(
                label: 'Please provide the `UserID` to be set as `super_admin`',
                required: true
            );

            $this->superAdmin = static::getUserModel()::findOrFail($superAdminId);
        } else {
            $this->superAdmin = $this->createSuperAdmin();
        }

        if (Utils::isTenancyEnabled()) {
            if (blank($tenantId)) {
                $this->components->error('Please provide the team/tenant id via `--tenant` option to assign the super admin to a team/tenant.');

                return self::FAILURE;
            }
            setPermissionsTeamId($tenantId);
            $this->superAdminRole = Utils::createRole(tenantId: $tenantId);
            $this->superAdminRole->syncPermissions(Utils::getPermissionModel()::pluck('id'));

        } else {
            $this->superAdminRole = Utils::createRole();
        }

        $this->superAdmin
            ->unsetRelation('roles')
            ->unsetRelation('permissions');

        $this->superAdmin
            ->assignRole($this->superAdminRole);

        $loginUrl = Filament::getCurrentOrDefaultPanel()?->getLoginUrl();

        $this->components->info("Success! {$this->superAdmin->email} may now log in at {$loginUrl}.");

        return self::SUCCESS;
    }

    protected function createSuperAdmin(): Authenticatable
    {
        return static::getUserModel()::create([
            'name' => text(label: 'Name', required: true),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    ! filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    static::getUserModel()::where('email', $email)->exists() => 'A user with this email address already exists',
                    default => null,
                },
            ),
            'password' => Hash::make(password(
                label: 'Password',
                required: true,
                validate: fn (string $value): ?string => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null
                }
            )),
        ]);
    }
}
