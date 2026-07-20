<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Tests\Fixtures\EnforcerPanelProvider;
use BezhanSalleh\FilamentShield\Tests\Fixtures\PluginProvidedPolicy;
use Filament\Events\ServingFilament;
use Filament\PanelRegistry;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->app->register(EnforcerPanelProvider::class);
    $this->app->forgetInstance(PanelRegistry::class);

    declareAppModel('App\Models\Blog\Article');
    declareAppModel('App\Models\Blog\Comment');
    declareAppModel('App\Models\Blog\Draft');

    declareAppPolicy('App\Policies\Blog\ArticlePolicy');
    declareAppPolicy('App\Policies\Blog\CommentPolicy');
});

it('registers mirror policies for guesser-blind placements at serving time', function () {
    FilamentShield::enforcePolicies();

    ServingFilament::dispatch();

    expect(Gate::policies())->toHaveKey('App\Models\Blog\Article')
        ->and(Gate::policies()['App\Models\Blog\Article'])->toBe('App\Policies\Blog\ArticlePolicy')
        ->and(Gate::getPolicyFor('App\Models\Blog\Article'))->toBeInstanceOf('App\Policies\Blog\ArticlePolicy');
});

it('registers nothing when the condition is false', function () {
    FilamentShield::enforcePolicies(false);

    ServingFilament::dispatch();

    expect(Gate::policies())->not->toHaveKey('App\Models\Blog\Article');
});

it('evaluates the condition lazily at serving time', function () {
    $enabled = false;

    FilamentShield::enforcePolicies(function () use (&$enabled): bool {
        return $enabled;
    });

    ServingFilament::dispatch();

    expect(Gate::policies())->not->toHaveKey('App\Models\Blog\Article');

    $enabled = true;

    ServingFilament::dispatch();

    expect(Gate::policies())->toHaveKey('App\Models\Blog\Article');
});

it('leaves excepted models untouched', function () {
    FilamentShield::enforcePolicies(except: ['App\Models\Blog\Comment']);

    ServingFilament::dispatch();

    expect(Gate::policies())->toHaveKey('App\Models\Blog\Article')
        ->and(Gate::policies())->not->toHaveKey('App\Models\Blog\Comment');
});

it('never overrides an existing explicit policy registration', function () {
    Gate::policy('App\Models\Blog\Article', PluginProvidedPolicy::class);

    FilamentShield::enforcePolicies();

    ServingFilament::dispatch();

    expect(Gate::policies()['App\Models\Blog\Article'])->toBe(PluginProvidedPolicy::class);
});

it('registers nothing for models without an existing mirror policy class', function () {
    FilamentShield::enforcePolicies();

    ServingFilament::dispatch();

    expect(Gate::policies())->not->toHaveKey('App\Models\Blog\Draft');
});
