<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Tests\TenancyTestCase;
use BezhanSalleh\FilamentShield\Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case Bindings
|--------------------------------------------------------------------------
|
| TestCase: Base test case without tenancy (no teams migrations)
| TenancyTestCase: Test case with tenancy enabled (teams migrations run)
|
| Directory structure determines which test case is used:
| - PluginTenancy/* -> TenancyTestCase (tenancy-specific tests)
| - Everything else -> TestCase (unit tests, feature tests, plugin tests)
|
*/

// TenancyTestCase for tenancy-specific tests only
uses(TenancyTestCase::class)
    ->in('PluginTenancy');

// TestCase for everything else
uses(TestCase::class)->in(
    'Feature',
    'Unit',
    'Plugin',
);
