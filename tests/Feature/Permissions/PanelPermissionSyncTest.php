<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Resources\Roles\Pages\EditRole;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('preserves other panel permissions when syncing a role', function () {
    config()->set('filament-shield.permissions.panel_prefix', true);

    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    $role = Role::create(['name' => 'tester', 'guard_name' => 'web']);
    Permission::create(['name' => 'system:View:User', 'guard_name' => 'web']);
    Permission::create(['name' => 'app:View:User', 'guard_name' => 'web']);
    $role->givePermissionTo(['system:View:User', 'app:View:User']);

    app(PermissionRegistrar::class)->forgetCachedPermissions();

    $page = new class extends EditRole
    {
        public function setRecord(Role $record): void
        {
            $this->record = $record;
        }

        public function setData(array $data): void
        {
            $this->data = $data;
        }

        public function setPermissions(Collection $permissions): void
        {
            $this->permissions = $permissions;
        }

        public function callAfterSave(): void
        {
            $this->afterSave();
        }
    };

    $page->setRecord($role);
    $page->setData(['guard_name' => 'web']);
    $page->setPermissions(collect(['system:View:User']));
    $page->callAfterSave();

    $role->refresh();

    expect($role->hasPermissionTo('system:View:User'))->toBeTrue();
    expect($role->hasPermissionTo('app:View:User'))->toBeTrue();
});
