<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends Factory<Role>
 */
class RoleFactory extends Factory
{
    protected $model = Role::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word() . '_' . $this->faker->randomNumber(4),
            'guard_name' => 'web',
        ];
    }

    /**
     * Set a specific guard name.
     */
    public function guard(string $guard): static
    {
        return $this->state(fn (array $attributes) => [
            'guard_name' => $guard,
        ]);
    }

    /**
     * Associate the role with a team/tenant.
     */
    public function forTeam(int $teamId): static
    {
        return $this->state(fn (array $attributes) => [
            'team_id' => $teamId,
        ]);
    }

    /**
     * Create a super admin role.
     */
    public function superAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => config('filament-shield.super_admin.name', 'super_admin'),
        ]);
    }

    /**
     * Create an admin role.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'admin',
        ]);
    }

    /**
     * Create an editor role.
     */
    public function editor(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'editor',
        ]);
    }

    /**
     * Create a viewer role.
     */
    public function viewer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'viewer',
        ]);
    }
}
