<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Commands\Concerns\CanGeneratePolicy;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\Applications\Application;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;

beforeEach(function () {
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

describe('generatePolicyPath', function () {
    it('generates correct policy path for a flat model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        // Reset the PSR-4 cache so it re-reads composer.json
        $reflection = new ReflectionClass(Utils::class);
        $prop = $reflection->getProperty('psr4Cache');
        $prop->setValue(null, null);

        $entity = [
            'modelFqcn' => User::class,
            'model' => 'User',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe($policyPath . DIRECTORY_SEPARATOR . 'UserPolicy.php');
    });

    it('generates correct policy path for a nested model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        $reflection = new ReflectionClass(Utils::class);
        $prop = $reflection->getProperty('psr4Cache');
        $prop->setValue(null, null);

        $entity = [
            'modelFqcn' => Application::class,
            'model' => 'Application',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe(
            $policyPath . DIRECTORY_SEPARATOR . 'Applications' . DIRECTORY_SEPARATOR . 'ApplicationPolicy.php'
        );
    });

    it('generates correct policy path for a flat model with the default policy path', function () {
        $policyPath = app_path('Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        $reflection = new ReflectionClass(Utils::class);
        $prop = $reflection->getProperty('psr4Cache');
        $prop->setValue(null, null);

        $entity = [
            'modelFqcn' => User::class,
            'model' => 'User',
        ];

        $result = $this->policyGenerator->callGeneratePolicyPath($entity);

        expect($result)->toBe($policyPath . DIRECTORY_SEPARATOR . 'UserPolicy.php');
    });
});

describe('generatePolicyStubVariables namespace', function () {
    it('generates correct namespace for a flat model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        $reflection = new ReflectionClass(Utils::class);
        $prop = $reflection->getProperty('psr4Cache');
        $prop->setValue(null, null);

        FilamentShield::shouldReceive('getResourcePolicyActionsWithPermissions')
            ->once()
            ->andReturn([]);

        $entity = [
            'modelFqcn' => User::class,
            'model' => 'User',
            'resourceFqcn' => 'DummyResource',
        ];

        $result = $this->policyGenerator->callGeneratePolicyStubVariables($entity);

        $expectedNamespace = Utils::resolveNamespaceFromPath($policyPath);
        expect($result['namespace'])->toBe($expectedNamespace);
    });

    it('generates correct namespace for a nested model with a non-default policy path', function () {
        $policyPath = app_path('Filament' . DIRECTORY_SEPARATOR . 'Policies');
        config()->set('filament-shield.policies.path', $policyPath);

        $reflection = new ReflectionClass(Utils::class);
        $prop = $reflection->getProperty('psr4Cache');
        $prop->setValue(null, null);

        FilamentShield::shouldReceive('getResourcePolicyActionsWithPermissions')
            ->once()
            ->andReturn([]);

        $entity = [
            'modelFqcn' => Application::class,
            'model' => 'Application',
            'resourceFqcn' => 'DummyResource',
        ];

        $result = $this->policyGenerator->callGeneratePolicyStubVariables($entity);

        $expectedNamespace = Utils::resolveNamespaceFromPath($policyPath) . '\\Applications';
        expect($result['namespace'])->toBe($expectedNamespace);
    });
});
