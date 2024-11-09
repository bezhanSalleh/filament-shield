<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Stringer;
use Filament\Panel;

trait CanMakePanelTenantable
{
    protected function makePanelTenantable(Panel $panel, string $panelPath, ?string $tenantModelClass): void
    {
        if (filled($tenantModelClass) && ! $panel->hasTenancy()) {

            Stringer::for($panelPath)
                ->prepend('->discoverResources', '->tenant(' . $tenantModelClass . ')')
                ->save();
            $this->activateTenancy($panelPath);

            $this->components->info("Panel `{$panel->getId()}` is now tenantable.");
        }

        if ($panel->hasTenancy()) {
            $this->activateTenancy($panelPath);

            $this->components->info("Panel `{$panel->getId()}` is now tenantable.");
        }
    }

    private function activateTenancy(string $panelPath): void
    {
        $stringer = Stringer::for($panelPath);

        $target = $stringer->contains('->plugins([') ? '->plugins([' : '->middleware([';
        $shieldMiddlewareImportStatement = 'use BezhanSalleh\FilamentShield\Middleware\SyncShieldTenant;';
        $shieldMiddleware = 'SyncShieldTenant::class,';
        $tenantMiddlewareMarker = '->tenantMiddleware([';

        if (! $stringer->contains($shieldMiddlewareImportStatement)) {
            $stringer->append('use', $shieldMiddlewareImportStatement);
        }

        $stringer->when(
            value: (! $stringer->contains($shieldMiddleware) && $stringer->contains($tenantMiddlewareMarker)),
            callback: fn (Stringer $stringer): bool => $stringer
                ->indent(4)
                ->append('->tenantMiddleware([', $shieldMiddleware)
                ->save()
        );
        $stringer->when(
            value: (! $stringer->contains($shieldMiddleware) && ! $stringer->contains($tenantMiddlewareMarker)),
            callback: fn (Stringer $stringer): bool => $stringer
                ->append($target, $tenantMiddlewareMarker, true)
                ->append($tenantMiddlewareMarker, '], isPersistent: true)')
                ->indent(4)
                ->prepend('], isPersistent: true)', $shieldMiddleware)
                ->save()
        );
    }
}
