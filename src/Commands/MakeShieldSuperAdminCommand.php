<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Hash;

use function Laravel\Prompts\{text, password};

class MakeShieldSuperAdminCommand extends Command
{
    public $signature = 'shield:super-admin
        {--user= : ID of user to be made super admin.}
        {--panel= : Panel ID to get the configuration from.}
    ';

    public $description = 'Creates Filament Super Admin';

    protected Authenticatable $superAdmin;

    public function handle(): int
    {
        if ($this->option('panel')) {
            Filament::setCurrentPanel(Filament::getPanel($this->option('panel')));
        }

        $auth = Filament::getCurrentPanel()?->auth();

        $userProvider = $auth->getProvider();

        if (Utils::getRoleModel()::whereName(Utils::getSuperAdminName())->doesntExist()) {
            FilamentShield::createRole();
        }

        if ($this->option('user')) {
            $this->superAdmin = $userProvider->getModel()::findOrFail($this->option('user'));
        } elseif ($usersCount = $userProvider->getModel()::count() === 1) {
            $this->superAdmin = $userProvider->getModel()::first();
        } elseif ($usersCount > 1) {
            $this->table(
                ['ID', 'Name', 'Email', 'Roles'],
                $userProvider->getModel()::with('roles')->get()->map(function (Authenticatable $user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => implode(',', $user->roles->pluck('name')->toArray()),
                    ];
                })
            );

            $superAdminId = text(
                label: 'Please provide the `UserID` to be set as `super_admin`',
                required: true
            );

            $this->superAdmin = $userProvider->getModel()::findOrFail($superAdminId);
        } else {
            $this->superAdmin = $this->createSuperAdmin($userProvider);
        }

        $this->superAdmin->assignRole(Utils::getSuperAdminName());

        $loginUrl = Filament::getCurrentPanel()?->getLoginUrl();

        $this->components->info("Success! {$this->superAdmin->email} may now log in at {$loginUrl}.");

        exit(self::SUCCESS);
    }

    protected function createSuperAdmin(EloquentUserProvider $provider): Authenticatable
    {
        return $provider->getModel()::create([
            'name' => text(label: 'Name', required: true),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: fn (string $email): ?string => match (true) {
                    !filter_var($email, FILTER_VALIDATE_EMAIL) => 'The email address must be valid.',
                    $provider->getModel()::where('email', $email)->exists() => 'A user with this email address already exists',
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
