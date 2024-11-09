<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Stringer;

trait CanRegisterPlugin
{
    protected function registerPlugin(string $panelPath, bool $centralApp, string $tenantModelClass): void
    {
        $stringer = Stringer::for($panelPath);

        $shieldPluginImportStatement = 'use BezhanSalleh\FilamentShield\FilamentShieldPlugin;';
        $shieldPlugin = 'FilamentShieldPlugin::make()';
        $pluginsArray = "->plugins([\n";
        $pluginsTarget = '->middleware([';

        if ($stringer->contains($shieldPlugin)) {
            $this->components->warn('Shield plugin is already registered! skipping...');
        } else {

            $stringer
                ->when(
                    value: ! $stringer->contains($shieldPluginImportStatement),
                    callback: fn (Stringer $stringer): Stringer => $stringer
                        ->append('use', $shieldPluginImportStatement)
                )
                ->when( /** @phpstan-ignore-next-line */
                    value: $stringer->contains($pluginsArray) && (! $stringer->contains($shieldPlugin)),
                    callback: fn (Stringer $stringer): Stringer => $stringer
                        ->when(
                            value: $centralApp,
                            callback: fn (Stringer $stringer) => $stringer
                                ->indent(4)
                                ->append($pluginsArray, $shieldPlugin)
                                ->append($shieldPlugin, '->centralApp(' . $tenantModelClass . '),'),
                            default: fn (Stringer $stringer) => $stringer
                                ->indent(4)
                                ->append($pluginsArray, $shieldPlugin . ',')
                        ),
                )
                ->when(/** @phpstan-ignore-next-line */
                    value: (! $stringer->contains($shieldPlugin) && ! $stringer->contains($pluginsArray)),
                    callback: fn (Stringer $stringer): Stringer => $stringer
                        ->when(
                            value: $centralApp,
                            callback: fn (Stringer $stringer) => $stringer
                                ->append($pluginsTarget, $pluginsArray, true)
                                ->append($pluginsArray, '])')
                                ->indent(4)
                                ->append($pluginsArray, $shieldPlugin)
                                ->append($shieldPlugin, '->centralApp(' . $tenantModelClass . '),'),
                            default: fn (Stringer $stringer) => $stringer
                                ->append($pluginsTarget, $pluginsArray, true)
                                ->append($pluginsArray, '])')
                                ->indent(4)
                                ->append($pluginsArray, $shieldPlugin . ',')
                        )
                )
                ->save();

            $this->components->info('Shield plugin has been registered successfully!');

        }

    }
}
