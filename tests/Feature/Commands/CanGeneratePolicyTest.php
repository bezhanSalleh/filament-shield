<?php

declare(strict_types=1);

use App\Domain\Users\Models\Account;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanGeneratePolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\LegacyUser;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Applications\Application;
use Modules\Blog\Models\DataModels\Draft;
use Modules\Blog\Models\Item;
use Spatie\Permission\Models\Role;
use Websrc\Models\Customer;

require_once __DIR__ . '/../../Fixtures/Modules/Blog/src/Models/Item.php';
require_once __DIR__ . '/../../Fixtures/Modules/Blog/src/Models/DataModels/Draft.php';
require_once __DIR__ . '/../../Fixtures/websrc/Models/Customer.php';
require_once __DIR__ . '/../../Fixtures/Domain/Users/Models/Account.php';

beforeEach(function () {
    declareAppModel('App\Models\Author');
    declareAppModel('App\Models\Blog\Category');

    $this->policyGenerator = new class
    {
        use CanGeneratePolicy;

        public function callGeneratePolicyPath(array $entity): string
        {
            return $this->generatePolicyPath($entity);
        }

        public function callGeneratePolicyStubVariables(array $entity): array
        {
            return $this->generatePolicyStubVariables($entity);
        }
    };
});

function policyStubVariablesFor(object $policyGenerator, string $model, string $modelFqcn): array
{
    FilamentShield::shouldReceive('getResourcePolicyActionsWithPermissions')
        ->once()
        ->andReturn([]);

    return $policyGenerator->callGeneratePolicyStubVariables([
        'modelFqcn' => $modelFqcn,
        'model' => $model,
        'resourceFqcn' => 'DummyResource',
    ]);
}

describe('generatePolicyPath', function () {
    it('generates correct policy path for a flat model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $entity = [
            'modelFqcn' => 'App\Models\Author',
            'model' => 'Author',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe($policyPath . DIRECTORY_SEPARATOR . 'AuthorPolicy.php');
    });

    it('generates correct policy path for a nested model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $entity = [
            'modelFqcn' => 'App\Models\Blog\Category',
            'model' => 'Category',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe(
            $policyPath . DIRECTORY_SEPARATOR . 'Blog' . DIRECTORY_SEPARATOR . 'CategoryPolicy.php'
        );
    });

    it('generates correct policy path for a flat model with the default policy path', function () {
        $policyPath = app_path('Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $entity = [
            'modelFqcn' => 'App\Models\Author',
            'model' => 'Author',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe($policyPath . DIRECTORY_SEPARATOR . 'AuthorPolicy.php');
    });

    it('generates correct policy path for a nested model with the default policy path', function () {
        $policyPath = app_path('Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $entity = [
            'modelFqcn' => 'App\Models\Blog\Category',
            'model' => 'Category',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe(
            $policyPath . DIRECTORY_SEPARATOR . 'Blog' . DIRECTORY_SEPARATOR . 'CategoryPolicy.php'
        );
    });
});

describe('generatePolicyStubVariables namespace', function () {
    it('generates correct namespace for a flat model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $result = policyStubVariablesFor($this->policyGenerator, 'Author', 'App\Models\Author');

        expect($result['namespace'])->toBe(Utils::resolveNamespaceFromPath($policyPath));
    });

    it('generates correct namespace for a nested model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        Utils::flushPsr4Cache();

        $result = policyStubVariablesFor($this->policyGenerator, 'Category', 'App\Models\Blog\Category');

        expect($result['namespace'])->toBe(Utils::resolveNamespaceFromPath($policyPath) . '\\Blog');
    });
});

describe('policy placement', function () {
    it('centralizes vendor model policies into the configured path', function () {
        $entity = [
            'modelFqcn' => Role::class,
            'model' => 'Role',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Role', Role::class);

        expect($path)->toBe(app_path('Policies') . DIRECTORY_SEPARATOR . 'RolePolicy.php')
            ->and($variables['namespace'])->toBe('App\Policies');
    });

    it('co-locates module src model policies beside their models with a discoverable namespace', function () {
        $entity = [
            'modelFqcn' => Item::class,
            'model' => 'Item',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Item', Item::class);

        expect($path)->toEndWith(implode(DIRECTORY_SEPARATOR, ['Fixtures', 'Modules', 'Blog', 'src', 'Policies', 'ItemPolicy.php']))
            ->and($variables['namespace'])->toBe('Modules\Blog\Policies');
    });

    it('replaces the Models directory itself, never deeper directory names ending in Models', function () {
        $entity = [
            'modelFqcn' => Draft::class,
            'model' => 'Draft',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Draft', Draft::class);

        expect($path)->toEndWith(implode(DIRECTORY_SEPARATOR, ['Fixtures', 'Modules', 'Blog', 'src', 'Policies', 'DataModels', 'DraftPolicy.php']))
            ->and($variables['namespace'])->toBe('Modules\Blog\Policies\DataModels');
    });

    it('no longer treats paths merely containing the src substring as central', function () {
        $entity = [
            'modelFqcn' => Customer::class,
            'model' => 'Customer',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Customer', Customer::class);

        expect($path)->toEndWith(implode(DIRECTORY_SEPARATOR, ['Fixtures', 'websrc', 'Policies', 'CustomerPolicy.php']))
            ->and($variables['namespace'])->toBe('Websrc\Policies');
    });

    it('keeps domain model policies beside their models', function () {
        $entity = [
            'modelFqcn' => Account::class,
            'model' => 'Account',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Account', Account::class);

        expect($path)->toEndWith(implode(DIRECTORY_SEPARATOR, ['Fixtures', 'Domain', 'Users', 'Policies', 'AccountPolicy.php']))
            ->and($variables['namespace'])->toBe('App\Domain\Users\Policies');
    });

    it('co-locates nested models of a non-app Models tree into a sibling Policies tree', function () {
        $entity = [
            'modelFqcn' => Application::class,
            'model' => 'Application',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'Application', Application::class);

        expect($path)->toEndWith(implode(DIRECTORY_SEPARATOR, ['Fixtures', 'Policies', 'Applications', 'ApplicationPolicy.php']))
            ->and($variables['namespace'])->toBe('BezhanSalleh\FilamentShield\Tests\Fixtures\Policies\Applications');
    });

    it('falls back to the configured path with a valid namespace for models outside any Models directory', function () {
        $entity = [
            'modelFqcn' => LegacyUser::class,
            'model' => 'LegacyUser',
        ];

        $path = $this->policyGenerator->callGeneratePolicyPath($entity);
        $variables = policyStubVariablesFor($this->policyGenerator, 'LegacyUser', LegacyUser::class);

        expect($path)->toBe(app_path('Policies') . DIRECTORY_SEPARATOR . 'LegacyUserPolicy.php')
            ->and($variables['namespace'])->toBe('App\Policies');
    });
});
