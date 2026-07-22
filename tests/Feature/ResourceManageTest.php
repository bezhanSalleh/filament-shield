<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;

beforeEach(function () {
    $this->shield = new class extends FilamentShield
    {
        public function policyMethodsFor(?string $resource = null): array
        {
            return $this->getDefaultPolicyMethodsOrFor($resource);
        }
    };
});

describe('resources.manage lookups', function () {
    it('applies the managed method list to the exact resource class', function () {
        config()->set('filament-shield.policies.merge', false);
        config()->set('filament-shield.resources.manage', [
            'App\Filament\Admin\Resources\UserResource' => ['viewAny', 'view'],
        ]);

        expect($this->shield->policyMethodsFor('App\Filament\Admin\Resources\UserResource'))
            ->toBe(['viewAny', 'view']);
    });

    it('keeps overrides separate for resources sharing a class basename', function () {
        config()->set('filament-shield.policies.merge', false);
        config()->set('filament-shield.resources.manage', [
            'App\Filament\Admin\Resources\UserResource' => ['viewAny', 'view', 'update'],
            'App\Filament\Client\Resources\UserResource' => ['viewAny'],
        ]);

        expect($this->shield->policyMethodsFor('App\Filament\Admin\Resources\UserResource'))
            ->toBe(['viewAny', 'view', 'update'])
            ->and($this->shield->policyMethodsFor('App\Filament\Client\Resources\UserResource'))
            ->toBe(['viewAny']);
    });

    it('matches manage keys verbatim without path interpretation', function () {
        config()->set('filament-shield.policies.merge', false);
        config()->set('filament-shield.resources.manage', [
            'App/Filament/Admin/Resources/UserResource' => ['viewAny'],
            'App/Filament/Client/Resources/UserResource' => ['viewAny', 'view'],
        ]);

        expect($this->shield->policyMethodsFor('App/Filament/Admin/Resources/UserResource'))
            ->toBe(['viewAny'])
            ->and($this->shield->policyMethodsFor('App/Filament/Client/Resources/UserResource'))
            ->toBe(['viewAny', 'view']);
    });

    it('falls back to the default methods for unmanaged resources', function () {
        config()->set('filament-shield.policies.merge', false);
        config()->set('filament-shield.resources.manage', [
            'App\Filament\Admin\Resources\UserResource' => ['viewAny'],
        ]);

        expect($this->shield->policyMethodsFor('App\Filament\Admin\Resources\PostResource'))
            ->toBe($this->shield->policyMethodsFor());
    });

    it('merges managed methods with the defaults when policies.merge is enabled', function () {
        config()->set('filament-shield.policies.merge', true);
        config()->set('filament-shield.resources.manage', [
            'App\Filament\Admin\Resources\UserResource' => ['publish'],
        ]);

        expect($this->shield->policyMethodsFor('App\Filament\Admin\Resources\UserResource'))
            ->toContain('publish')
            ->toContain('viewAny');
    });

    it('resolves the shipped RoleResource entry from the default config', function () {
        config()->set('filament-shield.policies.merge', false);

        expect($this->shield->policyMethodsFor(RoleResource::class))
            ->toBe(['viewAny', 'view', 'create', 'update', 'delete']);
    });
});
