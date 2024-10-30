<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use BezhanSalleh\FilamentShield\Stringer;

trait CanRegisterPlugin
{
    protected function registerPlugin(string $panelPath, bool $centralApp, string $modelOfPanelWithTenancy): void
    {
        $stringer = Stringer::for($panelPath);

        $shieldPluginImportStatement = 'use BezhanSalleh\FilamentShield\FilamentShieldPlugin;';
        $shieldPlugin = 'FilamentShieldPlugin::make()';
        $pluginsArrayMarker = "->plugins([\n";

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
                    value: $stringer->contains($pluginsArrayMarker) && (! $stringer->contains($shieldPlugin)),
                    callback: fn (Stringer $stringer): Stringer => $stringer
                        ->when(
                            value: $centralApp,
                            callback: fn (Stringer $stringer) => $stringer
                                ->indent(4)
                                ->append($pluginsArrayMarker, $shieldPlugin)
                                ->append($shieldPlugin, '->centralApp(' . $modelOfPanelWithTenancy . '),'),
                            default: fn (Stringer $stringer) => $stringer
                                ->indent(4)
                                ->append($pluginsArrayMarker, $shieldPlugin . ',')
                        ),
                )
                ->when(/** @phpstan-ignore-next-line */
                    value: (! $stringer->contains($shieldPlugin) && ! $stringer->contains($pluginsArrayMarker)),
                    callback: fn (Stringer $stringer): Stringer => $stringer
                        ->when(
                            value: $centralApp,
                            callback: fn (Stringer $stringer) => $stringer
                                ->prependBeforeLast('->', $pluginsArrayMarker)
                                ->append($pluginsArrayMarker, '])')
                                ->indent(4)
                                ->append($pluginsArrayMarker, $shieldPlugin)
                                ->append($shieldPlugin, '->centralApp(' . $modelOfPanelWithTenancy . '),'),
                            default: fn (Stringer $stringer) => $stringer
                                ->prependBeforeLast('->', $pluginsArrayMarker)
                                ->append($pluginsArrayMarker, '])')
                                ->indent(4)
                                ->append($pluginsArrayMarker, $shieldPlugin . ',')
                        )
                )
                ->save();

            $this->components->info('Shield plugin has been registered successfully!');

        }

    }
}
