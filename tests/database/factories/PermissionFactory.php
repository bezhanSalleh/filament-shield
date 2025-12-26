<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Permission;

/**
 * @extends Factory<Permission>
 */
class PermissionFactory extends Factory
{
    protected $model = Permission::class;

    /**
     * Create all standard resource permissions.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Permission>
     */
    public static function createResourcePermissions(string $resource, string $guard = 'web'): \Illuminate\Database\Eloquent\Collection
    {
        $permissions = collect([
            "view_{$resource}",
            "view_any_{$resource}",
            "create_{$resource}",
            "update_{$resource}",
            "delete_{$resource}",
            "delete_any_{$resource}",
            "force_delete_{$resource}",
            "force_delete_any_{$resource}",
            "restore_{$resource}",
            "restore_any_{$resource}",
            "reorder_{$resource}",
        ]);

        return $permissions->map(fn (string $name) => Permission::firstOrCreate([
            'name' => $name,
            'guard_name' => $guard,
        ]))->values();
    }

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
     * Create a view permission for a resource.
     */
    public function view(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "view_{$resource}",
        ]);
    }

    /**
     * Create a view_any permission for a resource.
     */
    public function viewAny(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "view_any_{$resource}",
        ]);
    }

    /**
     * Create a create permission for a resource.
     */
    public function forCreate(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "create_{$resource}",
        ]);
    }

    /**
     * Create an update permission for a resource.
     */
    public function update(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "update_{$resource}",
        ]);
    }

    /**
     * Create a delete permission for a resource.
     */
    public function delete(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "delete_{$resource}",
        ]);
    }

    /**
     * Create a delete_any permission for a resource.
     */
    public function deleteAny(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "delete_any_{$resource}",
        ]);
    }

    /**
     * Create a force_delete permission for a resource.
     */
    public function forceDelete(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "force_delete_{$resource}",
        ]);
    }

    /**
     * Create a force_delete_any permission for a resource.
     */
    public function forceDeleteAny(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "force_delete_any_{$resource}",
        ]);
    }

    /**
     * Create a restore permission for a resource.
     */
    public function restore(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "restore_{$resource}",
        ]);
    }

    /**
     * Create a restore_any permission for a resource.
     */
    public function restoreAny(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "restore_any_{$resource}",
        ]);
    }

    /**
     * Create a reorder permission for a resource.
     */
    public function reorder(string $resource): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => "reorder_{$resource}",
        ]);
    }
}
