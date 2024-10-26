<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Artisan;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Ensure the super_admin role exists
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);

        // Create a new user or update if exists
        $user = User::firstOrCreate(
            ['email' => 'superadmin@example.com'], // Unique identifier
            [
                'name' => 'Super Admin',
                'password' => bcrypt('securepassword'), // Replace with a strong password
            ]
        );

        // Assign the super_admin role to the user
        $user->assignRole($superAdminRole);

        // Check if permissions exist and assign them to the role
        $permissions = Permission::all();

        if ($permissions->isNotEmpty()) {
            // Sync all permissions to the super_admin role
            $superAdminRole->syncPermissions($permissions);
        } else {
            // Run the shield:generate command to generate permissions
            $this->command->info('No permissions found. Generating permissions...');
            Artisan::call('shield:generate', ['--all' => true]);

            // Re-fetch permissions after generation
            $permissions = Permission::all();
            $superAdminRole->syncPermissions($permissions);
            $this->command->info('Permissions generated and assigned to super_admin.');
        }
    }
}