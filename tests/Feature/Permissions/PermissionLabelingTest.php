<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;

beforeEach(function () {
    // Mock Filament panel to avoid NoDefaultPanelSetException
    $this->app->bind('filament', function () {
        return new class
        {
            public function getResources()
            {
                return [];
            }
        };
    });
});

describe('toLocalizationKey', function () {
    it('converts permission key to snake_case', function () {
        expect(Utils::toLocalizationKey('viewAny'))->toBe('view_any');
        expect(Utils::toLocalizationKey('deleteAny'))->toBe('delete_any');
        expect(Utils::toLocalizationKey('forceDelete'))->toBe('force_delete');
    });

    it('handles colon separator with pascal case', function () {
        config()->set('filament-shield.permissions.separator', ':');

        // ViewAny:User -> ViewAny_User -> view_any_user
        expect(Utils::toLocalizationKey('view:Dashboard'))->toBe('view_dashboard');
        expect(Utils::toLocalizationKey('ViewAny:User'))->toBe('view_any_user');
    });

    it('handles underscore separator with pascal case', function () {
        config()->set('filament-shield.permissions.separator', '_');

        // ViewAny_User -> ViewAny_User -> view_any__user -> view_any_user
        expect(Utils::toLocalizationKey('ViewAny_User'))->toBe('view_any_user');
        expect(Utils::toLocalizationKey('view_Dashboard'))->toBe('view_dashboard');
    });

    it('handles underscore separator with lower_snake case', function () {
        config()->set('filament-shield.permissions.separator', '_');

        // Already snake_case - should remain unchanged
        expect(Utils::toLocalizationKey('view_any_user'))->toBe('view_any_user');
        expect(Utils::toLocalizationKey('force_delete_any_post'))->toBe('force_delete_any_post');
    });

    it('handles keys without separator', function () {
        expect(Utils::toLocalizationKey('viewAny'))->toBe('view_any');
        expect(Utils::toLocalizationKey('view'))->toBe('view');
    });

    it('cleans up double underscores', function () {
        config()->set('filament-shield.permissions.separator', ':');

        // Ensures no double underscores in output
        expect(Utils::toLocalizationKey('view:_test'))->toBe('view_test');
    });
});

describe('getLocalizedLabel', function () {
    it('returns package translation when localization is disabled', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        // Package has translations for standard affixes
        expect($shield->getLocalizedLabel('view'))->toBe('View');
        expect($shield->getLocalizedLabel('view_any'))->toBe('View Any');
        expect($shield->getLocalizedLabel('create'))->toBe('Create');
    });

    it('falls back to headline when no translation exists', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        // Custom method not in package translations
        expect($shield->getLocalizedLabel('validate'))->toBe('Validate');
        expect($shield->getLocalizedLabel('publish_post'))->toBe('Publish Post');
    });

    it('uses provided fallback when no translation exists', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        expect($shield->getLocalizedLabel('custom_action', 'My Custom Action'))->toBe('My Custom Action');
    });

    it('checks user translation first when localization is enabled', function () {
        config()->set('filament-shield.localization.enabled', true);
        config()->set('filament-shield.localization.key', 'shield-permissions');

        $shield = new FilamentShield;

        // When user translation doesn't exist, falls back to package translation
        // The package has 'view' translated as 'View'
        expect($shield->getLocalizedLabel('view'))->toBe('View');

        // Custom keys not in package translations fall back to headline
        expect($shield->getLocalizedLabel('custom_action'))->toBe('Custom Action');
    });

    it('falls back to package translation when user translation missing', function () {
        config()->set('filament-shield.localization.enabled', true);
        config()->set('filament-shield.localization.key', 'shield-permissions');

        $shield = new FilamentShield;

        // Package translation should still work for standard affixes
        expect($shield->getLocalizedLabel('delete'))->toBe('Delete');
    });
});

describe('getAffixLabel', function () {
    it('returns localized label for resource affixes', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        expect($shield->getAffixLabel('view'))->toBe('View');
        expect($shield->getAffixLabel('viewAny'))->toBe('View Any');
        expect($shield->getAffixLabel('create'))->toBe('Create');
        expect($shield->getAffixLabel('update'))->toBe('Update');
        expect($shield->getAffixLabel('delete'))->toBe('Delete');
        expect($shield->getAffixLabel('deleteAny'))->toBe('Delete Any');
        expect($shield->getAffixLabel('forceDelete'))->toBe('Force Delete');
        expect($shield->getAffixLabel('forceDeleteAny'))->toBe('Force Delete Any');
        expect($shield->getAffixLabel('restore'))->toBe('Restore');
        expect($shield->getAffixLabel('restoreAny'))->toBe('Restore Any');
        expect($shield->getAffixLabel('replicate'))->toBe('Replicate');
        expect($shield->getAffixLabel('reorder'))->toBe('Reorder');
    });

    it('headlines custom affixes not in package translations', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        expect($shield->getAffixLabel('validate'))->toBe('Validate');
        expect($shield->getAffixLabel('publish'))->toBe('Publish');
        expect($shield->getAffixLabel('approveRequest'))->toBe('Approve Request');
    });
});

describe('getCustomPermissionLabel', function () {
    it('returns config label when localization is disabled', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        expect($shield->getCustomPermissionLabel('approve_posts', 'Approve Posts'))->toBe('Approve Posts');
        expect($shield->getCustomPermissionLabel('manage_settings', 'Manage Settings'))->toBe('Manage Settings');
    });

    it('headlines key when no config label provided', function () {
        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        expect($shield->getCustomPermissionLabel('approve_posts'))->toBe('Approve Posts');
        expect($shield->getCustomPermissionLabel('manage_settings'))->toBe('Manage Settings');
    });

    it('checks user translation when localization is enabled', function () {
        config()->set('filament-shield.localization.enabled', true);
        config()->set('filament-shield.localization.key', 'shield-permissions');

        $shield = new FilamentShield;

        // Falls back to headline when user translation doesn't exist
        expect($shield->getCustomPermissionLabel('approve_posts'))->toBe('Approve Posts');
    });
});

describe('TranslationsCommand key generation', function () {
    it('generates correct keys for affixes', function () {
        config()->set('filament-shield.permissions.separator', ':');

        // Standard affixes
        expect(Utils::toLocalizationKey('view'))->toBe('view');
        expect(Utils::toLocalizationKey('viewAny'))->toBe('view_any');
        expect(Utils::toLocalizationKey('forceDeleteAny'))->toBe('force_delete_any');
    });

    it('generates correct keys for page permissions', function () {
        config()->set('filament-shield.permissions.separator', ':');

        // Page permission keys
        expect(Utils::toLocalizationKey('view:Dashboard'))->toBe('view_dashboard');
        expect(Utils::toLocalizationKey('view:SettingsPage'))->toBe('view_settings_page');
    });

    it('generates correct keys for widget permissions', function () {
        config()->set('filament-shield.permissions.separator', ':');

        // Widget permission keys
        expect(Utils::toLocalizationKey('view:StatsOverview'))->toBe('view_stats_overview');
        expect(Utils::toLocalizationKey('view:RevenueWidget'))->toBe('view_revenue_widget');
    });
});

describe('localization fallback chain', function () {
    it('follows correct fallback order for affixes', function () {
        // 1. User translation (when enabled)
        // 2. Package resource_permission_prefixes_labels
        // 3. Headline

        config()->set('filament-shield.localization.enabled', false);

        $shield = new FilamentShield;

        // Step 2: Package translation exists
        expect($shield->getLocalizedLabel('view'))->toBe('View');

        // Step 3: No package translation, fallback to headline
        expect($shield->getLocalizedLabel('custom_action'))->toBe('Custom Action');
    });

    it('respects localization.enabled setting', function () {
        $shield = new FilamentShield;

        // When disabled, should still get package translations
        config()->set('filament-shield.localization.enabled', false);
        expect($shield->getLocalizedLabel('view'))->toBe('View');

        // When enabled but no user translation, should still get package translations
        config()->set('filament-shield.localization.enabled', true);
        config()->set('filament-shield.localization.key', 'shield-permissions');
        expect($shield->getLocalizedLabel('view'))->toBe('View');
    });
});
