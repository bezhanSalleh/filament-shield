<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use BezhanSalleh\FilamentShield\FilamentShield;

class MakeSuperAdminShieldCommand extends Command
{
    use Concerns\CanValidateInput;

    protected $hidden = true;

    public $signature = 'shield:super-admin';

    public $description = 'Creates Filament Super Admin';

    public function handle(): int
    {
        /** @var SessionGuard $auth */
        $auth = Filament::auth();

        /** @var EloquentUserProvider $userProvider */
        $userProvider = $auth->getProvider();

        if (!Role::whereName(config('filament-shield.default_roles.super_admin_role_name'))->exists()) {
            Role::create(
                ['name' => config('filament-shield.default_roles.super_admin_role_name')],
                ['guard_name' => config('filament.auth.guard')]
            );
        }

        if (config('filament-shield.default_roles.filament_user.enabled') && !Role::whereName(config('filament-shield.default_roles.filament_user.role_name'))->exists()) {
            Role::create(
                ['name' => config('filament-shield.default_roles.filament_user.role_name')],
                ['guard_name' => config('filament.auth.guard')]
            );
        }

        if ($userProvider->getModel()::count() === 1) {
            $superAdmin = $userProvider->getModel()::first();

            $superAdmin->assignRole(config('filament-shield.default_roles.super_admin_role_name'));

            if (config('filament-shield.default_roles.filament_user.enabled')) {
                $superAdmin->assignRole(config('filament-shield.default_roles.filament_user.role_name'));
            }
        }
        else if ($userProvider->getModel()::count() > 1) {
            $this->table(
                ['ID','Name','Email','Roles'],
                $userProvider->getModel()::get()->map(function($user){
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => implode(',',$user->roles->pluck('name')->toArray())
                    ];
                })
            );

            $superAdminId = $this->ask('Please provide the `UserID` to be set as `super_admin`');

            $superAdmin = $userProvider->getModel()::find($superAdminId);

            $superAdmin->assignRole(config('filament-shield.default_roles.super_admin_role_name'));

            if (config('filament-shield.default_roles.filament_user.enabled')) {
                $superAdmin->assignRole(config('filament-shield.default_roles.filament_user.role_name'));
            }
        }
        else {
            $superAdmin = $userProvider->getModel()::create([
                'name' => $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']),
                'email' => $this->validateInput(fn () => $this->ask('Email address'), 'email', ['required', 'email', 'unique:' . $userProvider->getModel()]),
                'password' => Hash::make($this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8'])),
            ]);

            $superAdmin->assignRole(config('filament-shield.default_roles.super_admin_role_name'));

            if (config('filament-shield.default_roles.filament_user.enabled')) {
                $superAdmin->assignRole(config('filament-shield.default_roles.filament_user.role_name'));
            }
        }

        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$superAdmin->email} may now log in at {$loginUrl}.");

        return self::SUCCESS;
    }
}
