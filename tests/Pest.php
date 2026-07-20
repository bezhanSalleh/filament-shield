<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Tests\TenancyTestCase;
use BezhanSalleh\FilamentShield\Tests\TestCase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

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

/*
|--------------------------------------------------------------------------
| App Skeleton Fixtures
|--------------------------------------------------------------------------
|
| Policy placement is driven by where a model lives on disk, so app-tree
| scenarios need real classes inside the test skeleton's app directory.
| Classes are written once and required manually since the skeleton is
| not composer-autoloaded during package tests.
|
*/

function declareAppClass(string $class, string $contents): void
{
    if (class_exists($class, false)) {
        return;
    }

    $path = app_path(str_replace('\\', DIRECTORY_SEPARATOR, Str::after($class, 'App\\')) . '.php');

    if (! is_file($path)) {
        File::ensureDirectoryExists(dirname($path));
        File::put($path, $contents);
    }

    require_once $path;
}

function declareAppModel(string $model): void
{
    $namespace = Str::beforeLast($model, '\\');
    $class = class_basename($model);

    declareAppClass($model, <<<PHP
    <?php

    namespace {$namespace};

    use Illuminate\Database\Eloquent\Model;

    class {$class} extends Model
    {
        protected \$guarded = [];
    }

    PHP);
}

function declareAppPolicy(string $policy): void
{
    $namespace = Str::beforeLast($policy, '\\');
    $class = class_basename($policy);

    declareAppClass($policy, <<<PHP
    <?php

    namespace {$namespace};

    class {$class}
    {
    }

    PHP);
}
