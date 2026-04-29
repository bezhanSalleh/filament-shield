<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Commands\Concerns\CanGeneratePolicy;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Models\User;
use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Filesystem\Filesystem;

afterEach(function () {
    Filament::setCurrentPanel(null);
});

it('keeps base policy path when panel policy path is disabled', function () {
    $panel = Panel::make()->id('admin');
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.panel_path', false);
    config()->set('filament-shield.policies.path', app_path('Policies'));

    $path = Utils::getPolicyPath();

    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'Policies');
    expect($path)->not->toEndWith(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'Admin');
});

it('keeps base policy path for default panel when enabled', function () {
    $panel = Panel::make()->id('app')->default();
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.path', app_path('Policies'));

    $path = Utils::getPolicyPath();

    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'Policies');
    expect($path)->not->toEndWith(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'App');
});

it('appends panel segment to policy path when enabled for non-default panel', function () {
    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.path', app_path('Policies'));

    $path = Utils::getPolicyPath();

    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'System');
});

it('resolves role policy path within the panel policy directory', function () {
    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.path', app_path('Policies'));

    $policyPath = Utils::getPolicyPath();
    $filesystem = new Filesystem;
    $filesystem->ensureDirectoryExists($policyPath);
    $filesystem->put($policyPath . DIRECTORY_SEPARATOR . 'RolePolicy.php', '<?php');

    $rolePolicy = Utils::getRolePolicyPath();

    expect($rolePolicy)->toBe('App\\Policies\\System\\RolePolicy');
});

it('mirrors model subfolders under the panel policy path', function () {
    $panel = Panel::make()->id('system');
    Filament::setCurrentPanel($panel);

    config()->set('filament-shield.policies.panel_path', true);
    config()->set('filament-shield.policies.force_path', false);

    $generator = new class
    {
        use CanGeneratePolicy;

        public function pathFor(string $modelFqcn): string
        {
            $entity = [
                'resourceFqcn' => '',
                'model' => class_basename($modelFqcn),
                'modelFqcn' => $modelFqcn,
            ];

            return $this->generatePolicyPath($entity);
        }
    };

    $path = $generator->pathFor(User::class);

    expect($path)->toContain(DIRECTORY_SEPARATOR . 'Policies' . DIRECTORY_SEPARATOR . 'System' . DIRECTORY_SEPARATOR);
    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'UserPolicy.php');
});

it('honors configured policy path outside app when force path is disabled', function () {
    $customPath = base_path('custom-policies');

    config()->set('filament-shield.policies.path', $customPath);
    config()->set('filament-shield.policies.panel_path', false);
    config()->set('filament-shield.policies.force_path', false);

    $generator = new class
    {
        use CanGeneratePolicy;

        public function pathFor(string $modelFqcn): string
        {
            $entity = [
                'resourceFqcn' => '',
                'model' => class_basename($modelFqcn),
                'modelFqcn' => $modelFqcn,
            ];

            return $this->generatePolicyPath($entity);
        }
    };

    $path = $generator->pathFor(User::class);

    expect($path)->toStartWith($customPath);
    expect($path)->toEndWith(DIRECTORY_SEPARATOR . 'UserPolicy.php');
});
