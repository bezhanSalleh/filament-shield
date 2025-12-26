<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests;

use BezhanSalleh\FilamentShield\Tests\Fixtures\Concerns\InteractsWithTenancy;
use Spatie\Permission\PermissionRegistrar;

/**
 * Base test case for tests that require tenancy and admin panel.
 */
class TenancyTestCase extends TestCase
{
    use InteractsWithTenancy;

    protected bool $withPanel = true;

    protected bool $withTenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        $registrar = app(PermissionRegistrar::class);
        $registrar->initializeCache();
        $registrar->forgetCachedPermissions();

        $this->setUpTenancy();
    }

    public function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config()->set('permission.teams', true);
    }
}
