<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Models\Setting;

class ShieldSettingSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        /** @var array[string]mixed $settingKeys */
        $settingKeys = [
            'super_admin' => [
                'enabled' => true,
                'name'  => 'super_admin'
            ],
            'filament_user' => [
                'enabled' => true,
                'name' => 'filament_user'
            ],

            'permission_prefixes' => [
                'resource' => [
                    'view',
                    'view_any',
                    'create',
                    'delete',
                    'delete_any',
                    'update',
                    'export',
                ],
                'page' => 'page',
                'widget' => 'widget',
            ],

            'entities' => [
                'pages' => true,
                'widgets' => true,
                'resources' => true,
                'custom_permissions' => false,
            ],
            'generator' => [
                'option' => 'policies_and_permissions'
            ],
            'exclude' => [
                'enabled' => true,
                'pages' => [
                    'Dashboard',
                ],
                'widgets' => [
                    'AccountWidget','FilamentInfoWidget',
                ],
                'resources' => [],
            ],
            'register_role_policy' => [
                'enabled' => false
            ],
        ];

        if (Setting::count()) {
            Setting::truncate();
        }

        foreach ($settingKeys as $key => $value) {
            Setting::create([
                'key' => $key,
                'value' => $value,
                'default' => $value
            ]);
        }

        $this->command->info('Shield settings created.');
    }
}
