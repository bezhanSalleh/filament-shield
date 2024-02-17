<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class MakeShieldSuperAdminCommand extends Command
{
    public $signature = 'shield:super-admin
        {--user= : ID of user to be made super admin.}
        {--panel= : Panel ID to get the configuration from.}
    ';

    public $description = 'Creates Filament Super Admin';

    protected Authenticatable $superAdmin;

    protected function getAuthGuard(): Guard
    {
        if ($this->option('panel')) {
            Filament::setCurrentPanel(Filament::getPanel($this->option('panel')));
        }

        return Filament::getCurrentPanel()?->auth();
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
        $usersCount = static::getUserModel()::count();

        if (Utils::getRoleModel()::whereName(Utils::getSuperAdminName())->doesntExist()) {
            FilamentShield::createRole();
        }

        if ($this->option('user')) {
            $this->superAdmin = static::getUserModel()::findOrFail($this->option('user'));
        } elseif ($usersCount === 1) {
            $this->superAdmin = static::getUserModel()::first();
        } elseif ($usersCount > 1) {
            $this->table(
                ['ID', 'Name', 'Email', 'Roles'],
                static::getUserModel()::with('roles')->get()->map(function (Authenticatable $user) {
                    return [
                        'id' => $user->getKey(),
                        'name' => $user->getAttribute('name'),
                        'email' => $user->getAttribute('email'),
                        /** @phpstan-ignore-next-line */
                        'roles' => implode(',', $user->roles->pluck('name')->toArray()),
                    ];
                })
            );

            $superAdminId = text(
                label: 'Please provide the `UserID` to be set as `super_admin`',
                required: true
            );

            $this->superAdmin = static::getUserModel()::findOrFail($superAdminId);
        } else {
            $this->superAdmin = $this->createSuperAdmin();
        }

        $this->superAdmin->assignRole(Utils::getSuperAdminName());

        $loginUrl = Filament::getCurrentPanel()?->getLoginUrl();

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
                validate: fn (string $value) => match (true) {
                    strlen($value) < 8 => 'The password must be at least 8 characters.',
                    default => null
                }
            )),
        ]);
    }
}
