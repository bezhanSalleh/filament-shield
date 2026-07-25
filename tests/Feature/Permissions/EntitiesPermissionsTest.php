<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;

function shieldWithEntities(): FilamentShield
{
    return new class extends FilamentShield
    {
        public function getAllResourcePermissionsWithLabels(): array
        {
            return [
                'View:Article' => 'View',
                'Update:Article' => 'Update',
            ];
        }

        public function getPages(): ?array
        {
            return [
                'App\Filament\Pages\Settings' => [
                    'pageFqcn' => 'App\Filament\Pages\Settings',
                    'permissions' => ['View:Settings' => 'Settings'],
                ],
            ];
        }

        public function getWidgets(): ?array
        {
            return [
                'App\Filament\Widgets\IncomeWidget' => [
                    'widgetFqcn' => 'App\Filament\Widgets\IncomeWidget',
                    'permissions' => ['View:IncomeWidget' => 'Income Widget'],
                ],
            ];
        }

        public function getCustomPermissions(bool $localized = false): ?array
        {
            return ['Impersonate:User' => 'Impersonate User'];
        }
    };
}

it('includes the permission keys of pages and widgets', function () {
    expect(shieldWithEntities()->getEntitiesPermissions())
        ->toBe([
            'View:Article',
            'Update:Article',
            'View:Settings',
            'View:IncomeWidget',
            'Impersonate:User',
        ]);
});

it('does not leak page and widget class names into the permission list', function () {
    expect(shieldWithEntities()->getEntitiesPermissions())
        ->not->toContain('App\Filament\Pages\Settings')
        ->not->toContain('App\Filament\Widgets\IncomeWidget');
});

it('handles entities without permissions', function () {
    $shield = new class extends FilamentShield
    {
        public function getAllResourcePermissionsWithLabels(): array
        {
            return ['View:Article' => 'View'];
        }

        public function getPages(): ?array
        {
            return ['App\Filament\Pages\Settings' => ['pageFqcn' => 'App\Filament\Pages\Settings']];
        }

        public function getWidgets(): ?array
        {
            return null;
        }

        public function getCustomPermissions(bool $localized = false): ?array
        {
            return null;
        }
    };

    expect($shield->getEntitiesPermissions())->toBe(['View:Article']);
});
