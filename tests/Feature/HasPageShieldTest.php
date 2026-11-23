<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Pages\GeneralSettings;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Pages\SeoSettings;

it('resolves distinct permissions for sibling pages that share HasPageShield on an abstract parent', function () {
    FilamentShield::shouldReceive('getPages')
        ->andReturn([
            GeneralSettings::class => [
                'permissions' => ['View:General' => 'View:General'],
            ],
            SeoSettings::class => [
                'permissions' => ['View:Seo' => 'View:Seo'],
            ],
        ]);

    $resolve = function (string $page): ?string {
        $method = new \ReflectionMethod($page, 'getPagePermission');
        $method->setAccessible(true);

        return $method->invoke(null);
    };

    expect($resolve(GeneralSettings::class))->toBe('View:General')
        ->and($resolve(SeoSettings::class))->toBe('View:Seo')
        ->and($resolve(GeneralSettings::class))->toBe('View:General');
});
