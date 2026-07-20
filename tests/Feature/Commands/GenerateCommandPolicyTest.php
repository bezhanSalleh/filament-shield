<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Tests\Fixtures\GeneratorPanelProvider;
use BezhanSalleh\FilamentShield\Tests\Fixtures\PluginProvidedPolicy;
use Filament\PanelRegistry;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Modules\Blog\Models\Item;
use Spatie\Permission\Models\Permission;

require_once __DIR__ . '/../../Fixtures/Modules/Blog/src/Models/Item.php';

function modulePoliciesPath(): string
{
    return dirname((string) (new ReflectionClass(Item::class))->getFileName(), 2)
        . DIRECTORY_SEPARATOR . 'Policies';
}

beforeEach(function () {
    $this->app->register(GeneratorPanelProvider::class);
    $this->app->forgetInstance(PanelRegistry::class);

    declareAppModel('App\Models\Author');
    declareAppModel('App\Models\Editor');
    declareAppModel('App\Models\Reporter');
});

afterEach(function () {
    File::delete([
        app_path('Policies' . DIRECTORY_SEPARATOR . 'AuthorPolicy.php'),
        app_path('Policies' . DIRECTORY_SEPARATOR . 'EditorPolicy.php'),
        app_path('Policies' . DIRECTORY_SEPARATOR . 'ReporterPolicy.php'),
    ]);

    File::deleteDirectory(modulePoliciesPath());
});

it('generates app model policies into the configured path with the configured namespace', function () {
    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies',
        '--resource' => 'AuthorResource',
    ])->assertSuccessful();

    $policyPath = app_path('Policies' . DIRECTORY_SEPARATOR . 'AuthorPolicy.php');

    expect(File::exists($policyPath))->toBeTrue()
        ->and(File::get($policyPath))->toContain('namespace App\Policies;')
        ->and(File::get($policyPath))->toContain('use App\Models\Author;')
        ->and(File::get($policyPath))->toContain('class AuthorPolicy');
});

it('generates module policies beside their models where Laravel discovers them', function () {
    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies',
        '--resource' => 'ItemResource',
    ])->assertSuccessful();

    $policyPath = modulePoliciesPath() . DIRECTORY_SEPARATOR . 'ItemPolicy.php';

    expect(File::exists($policyPath))->toBeTrue()
        ->and(File::get($policyPath))->toContain('namespace Modules\Blog\Policies;')
        ->and(File::get($policyPath))->toContain('class ItemPolicy');
});

it('skips a model whose policy is provided elsewhere but still generates its permissions', function () {
    Gate::policy('App\Models\Author', PluginProvidedPolicy::class);

    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies_and_permissions',
        '--resource' => 'AuthorResource',
    ])
        ->expectsOutputToContain(PluginProvidedPolicy::class)
        ->assertSuccessful();

    expect(File::exists(app_path('Policies' . DIRECTORY_SEPARATOR . 'AuthorPolicy.php')))->toBeFalse()
        ->and(Permission::where('name', 'ViewAny:Author')->exists())->toBeTrue();
});

it('takes over a provided policy once a class exists at the mirror location', function () {
    Gate::policy('App\Models\Editor', PluginProvidedPolicy::class);

    declareAppPolicy('App\Policies\EditorPolicy');

    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies',
        '--resource' => 'EditorResource',
    ])->assertSuccessful();

    $policyPath = app_path('Policies' . DIRECTORY_SEPARATOR . 'EditorPolicy.php');

    expect(File::get($policyPath))->toContain('use App\Models\Editor;')
        ->and(File::get($policyPath))->toContain('class EditorPolicy');
});

it('keeps updating its own policy on plain re-runs while the provided policy still resolves', function () {
    Gate::policy('App\Models\Reporter', PluginProvidedPolicy::class);

    declareAppPolicy('App\Policies\ReporterPolicy');

    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies',
        '--resource' => 'ReporterResource',
    ])->assertSuccessful();

    $policyPath = app_path('Policies' . DIRECTORY_SEPARATOR . 'ReporterPolicy.php');

    File::put($policyPath, '<?php // stale');

    $this->artisan('shield:generate', [
        '--panel' => 'generator',
        '--option' => 'policies',
        '--resource' => 'ReporterResource',
    ])->assertSuccessful();

    expect(File::get($policyPath))->toContain('class ReporterPolicy')
        ->and(File::get($policyPath))->not->toContain('stale');
});
