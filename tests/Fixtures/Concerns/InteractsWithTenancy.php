<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures\Concerns;

use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Team;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;

trait InteractsWithTenancy
{
    protected ?Team $tenant = null;

    protected ?User $tenantUser = null;

    protected function setUpTenancy(): void
    {
        // Enable teams in Spatie Permission
        config()->set('permission.teams', true);

        // Clear permission cache to pick up new config
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    protected function createTenant(array $attributes = []): Team
    {
        $this->tenant = Team::factory()->create($attributes);

        return $this->tenant;
    }

    protected function createTenantUser(array $attributes = []): User
    {
        $this->tenantUser = User::factory()->create($attributes);

        if ($this->tenant) {
            $this->tenantUser->teams()->attach($this->tenant->id);
        }

        return $this->tenantUser;
    }

    protected function setTenant(?Team $tenant = null): void
    {
        $tenant = $tenant ?? $this->tenant;

        if ($tenant) {
            // Only set Filament tenant if user is authenticated
            // Filament::setTenant() requires an authenticated user
            if (auth()->check()) {
                Filament::setTenant($tenant);
            }

            // Always set Spatie Permission team context
            setPermissionsTeamId($tenant->id);
        }
    }

    protected function actingAsTenantUser(?User $user = null, ?Team $tenant = null): static
    {
        $user = $user ?? $this->tenantUser;
        $tenant = $tenant ?? $this->tenant;

        $this->actingAs($user);
        $this->setTenant($tenant);

        return $this;
    }
}
