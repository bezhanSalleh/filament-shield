<?php
// config for BezhanSalleh/FilamentShield
return [

    'default_roles' => [
        'super_admin_role_name' => 'super_admin',
        'filament_user' => [
            'role_name' => 'filament_user',
            'enabled' => true
        ],
    ],

    'default_permission_prefixes' => [
        'view',
        'view_any',
        'create',
        'delete',
        'delete_any',
        'update',
    ],
];
