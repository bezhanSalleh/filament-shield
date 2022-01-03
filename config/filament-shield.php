<?php
// config for BezhanSalleh/FilamentShield
return [

    'super_admin' => [
        'enabled' => true,
        'role_name' => 'super_admin',
    ],

    'filament_user' => [
        'enabled' => true,
        'role_name' => 'filament_user',
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
