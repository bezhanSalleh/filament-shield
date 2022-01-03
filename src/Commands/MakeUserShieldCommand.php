<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeUserShieldCommand extends Command
{
    use Concerns\CanValidateInput;

    protected $hidden = true;

    public $signature = 'shield:user';

    public $description = 'Creates Filament user with Shields enabled';

    public function handle(): int
    {
        /** @var SessionGuard $auth */
        $auth = Filament::auth();

        /** @var EloquentUserProvider $userProvider */
        $userProvider = $auth->getProvider();

        $userModel = $userProvider->getModel();

        $user = $userModel::create([
            'name' => $this->validateInput(fn () => $this->ask('Name'), 'name', ['required']),
            'email' => $this->validateInput(fn () => $this->ask('Email address'), 'email', ['required', 'email', 'unique:' . $userModel]),
            'password' => Hash::make($this->validateInput(fn () => $this->secret('Password'), 'password', ['required', 'min:8'])),
        ]);

        if ($userModel::count() === 0) {
            if (FilamentShield::isSuperAdminEnabled()) {
                $user->assignRole(config('filament-shield.super_admin.role_name'));
            }

            if (FilamentShield::isFilamentUserEnabled()) {
                $user->assignRole(config('filament-shield.filament_user.role_name'));
            }
        } else {
            $choice = $this->choice('What role the user should have?', [
                config('filament-shield.super_admin.role_name'),
                config('filament-shield.filament_user.role_name'),
            ], 0, null, false);
        }

        $loginUrl = route('filament.auth.login');
        $this->info("Success! {$user->email} may now log in at {$loginUrl}.");

        return self::SUCCESS;
    }
}
