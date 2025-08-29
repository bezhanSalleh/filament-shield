<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

it('has a valid test', function () {
    $resource = RoleResource::class;
    dd($resource::getTenantRelationshipName());
});