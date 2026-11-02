<?php

declare(strict_types=1);

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Pages\BaseSettings;
use BezhanSalleh\FilamentShield\Tests\Fixtures\Widgets\BaseStatsWidget;

it('resolves the page permission per class when pages share a parent using the trait', function () {
    $general = (new class extends BaseSettings {})::class;
    $seo = (new class extends BaseSettings {})::class;

    FilamentShield::shouldReceive('getPages')->andReturn([
        $general => ['permissions' => ['View:General' => 'General']],
        $seo => ['permissions' => ['View:Seo' => 'Seo']],
    ]);

    $permission = fn (string $page): ?string => (new ReflectionMethod($page, 'getPagePermission'))->invoke(null);

    expect($permission($general))->toBe('View:General')
        ->and($permission($seo))->toBe('View:Seo');
});

it('resolves the widget permission per class when widgets share a parent using the trait', function () {
    $orders = (new class extends BaseStatsWidget {})::class;
    $revenue = (new class extends BaseStatsWidget {})::class;

    FilamentShield::shouldReceive('getWidgets')->andReturn([
        $orders => ['permissions' => ['View:OrdersWidget' => 'Orders']],
        $revenue => ['permissions' => ['View:RevenueWidget' => 'Revenue']],
    ]);

    $permission = fn (string $widget): ?string => (new ReflectionMethod($widget, 'getWidgetPermission'))->invoke(null);

    expect($permission($orders))->toBe('View:OrdersWidget')
        ->and($permission($revenue))->toBe('View:RevenueWidget');
});
