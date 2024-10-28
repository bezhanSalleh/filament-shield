<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Panel;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use BezhanSalleh\FilamentShield\Stringer;
use Symfony\Component\Console\Attribute\AsCommand;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;

#[AsCommand(name: 'shield:init', description: 'Setup core package requirements and initialize Shield')]
class ShieldInitCommand extends Command
{
    use CanManipulateFiles;

    /** @var string */
    protected $signature = 'shield:init {--panel=} {--central}';

    /** @var string */
    protected $description = 'Setup core package requirements and initialize Shield';

    public function handle(): int
    {
        $panel = Filament::getPanel($this->option('panel') ?? null);

        if ($panel->hasTenancy() && $this->option('central')) {
            $this->components->warn('Cannot install Shield as `Central` on a tenant panel!');
            return static::FAILURE;
        }

        if (!$panel->hasTenancy() && $this->option('central') && blank(static::getPanelWithTenancySupport()) ) {
            $this->components->warn('Cannot install Shield as `Central` without, at-least a panel with tenancy support!');
            return static::INVALID;
        }

        $panelPath = app_path(
            (string) str($panel->getId())
                ->studly()
                ->append('PanelProvider')
                ->prepend('Providers/Filament/')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $this->fileExists($panelPath)) {
            $this->error("Panel not found: {$panelPath}");
            return static::FAILURE;
        }

        $stringer = Stringer::for($panelPath);

        $shieldNeedle = 'FilamentShieldPlugin::make()';
        $pluginsNeedle = '->plugins([';

        if ($stringer->contains($shieldNeedle)) {
            $this->components->warn('Shield is already installed!');
        }

        if (! $stringer->contains($shieldNeedle) && $stringer->contains($pluginsNeedle)) {
            $stringer->when(
                value: $this->option('central'),
                callback: fn (Stringer $stringer) => $stringer
                    ->indent(4)
                    ->append($pluginsNeedle,$shieldNeedle)
                    ->indent(4)
                    ->append($shieldNeedle, "->centralApp(" . static::getTenantModelClass() . "),"),
                default: fn (Stringer $stringer) => $stringer
                    ->indent(4)
                    ->append($pluginsNeedle, $shieldNeedle.",")
            );
        }

        if (! $stringer->contains($shieldNeedle) && ! $stringer->contains($pluginsNeedle)) {
            ray('here second');

            $stringer->when(
                value: $this->option('central'),
                callback: fn (Stringer $stringer) => $stringer
                    ->replaceLast(");", "])Marker")
                    ->append("Marker", $pluginsNeedle)
                    ->append($pluginsNeedle, "]);")
                    ->indent(4)
                    ->prepend("]);", $shieldNeedle)
                    ->indent(4)
                    ->prepend("]);", "->centralApp(" . static::getTenantModelClass() . "),")
                    ->replace("Marker", "])"),
                default: fn (Stringer $stringer) => $stringer
                    ->replaceLast(");", "])Marker")
                    ->append("Marker", $pluginsNeedle)
                    ->append($pluginsNeedle, "]);")
                    ->indent(4)
                    ->prepend("]);", $shieldNeedle . ",")
                    ->replace("Marker", "])")
            );
        }
        $stringer->save();
        ray($stringer)->die();
        // $stringer->when(
        //     value: ! $stringer->contains("FilamentShield"),
        //     callback: fn(Stringer $stringer) =>
        //         $stringer->when(
        //             value: $stringer->contains("->plugins(["),
        //             callback: fn(Stringer $stringer) =>
        //                 $stringer->when(
        //                     value: $this->option('central'),
        //                     callback: fn (Stringer $stringer) => $stringer
        //                         ->indent(4)
        //                         ->append("->plugins([",$newNeedle = "FilamentShieldPlugin::make()")
        //                         ->indent(4)
        //                         ->append($newNeedle, "->centralApp(" . static::getTenantModelClass() . "),"),
        //                     default: fn (Stringer $stringer) => $stringer
        //                         ->indent(4)
        //                         ->append("->plugins([", "FilamentShieldPlugin::make()")
        //                 ),
        //             default: fn(Stringer $stringer) =>
        //                 $stringer->when(
        //                     value: $this->option('central'),
        //                     callback: fn (Stringer $stringer) => $stringer
        //                         ->replaceLast(");", "')
        //                             ->plugins(["
        //                         )
        //                         ->append("->plugins([",$newNeedle = "FilamentShieldPlugin::make()")
        //                         ->indent(4)
        //                         ->append($newNeedle, "->centralApp(" . static::getTenantModelClass() . "),"),
        //                     default: fn (Stringer $stringer) => $stringer
        //                         ->replaceLast(");", "])Marker")
        //                         ->append("Marker", "->plugins([")
        //                         ->indent(4)
        //                         ->append("->plugins([", "FilamentShieldPlugin::make()]);")
        //                         ->replace("Marker", ")]")
        //                 )
        //             ),
        //     default: fn () => $this->components->warn('Shield is already installed!')
        // );
        // // ->save();

        // if (preg_match('/FilamentShieldPlugin::make\(\)(?:\s*->\s*\w+\(\))*/', $fileContent)) {
        //     $this->components->warn('Shield is already installed!');
        //     return static::INVALID;
        // }

        // preg_match('/^(\s*)->/m', $fileContent, $indentMatch);

        // $baseIndent = $indentMatch[1] ?? '';

        // $pluginsPattern = '/(->plugins\(\s*\[\s*)([\s\S]*?)(\s*\]\s*\))/';

        // if (preg_match($pluginsPattern, $fileContent, $matches)) {
        //     $newPluginDefinition = '\BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()' . ($this->option('central') ?
        //             "\n" . $baseIndent . '        ->centralApp(' . static::getTenantModelClass() . '),' : ',') .
        //         PHP_EOL.$baseIndent.str_repeat(' ', 4);

        //     $updatedContent = preg_replace($pluginsPattern, '$1' . $newPluginDefinition . '$2$3', $fileContent);

        //     file_put_contents($panelPath, $updatedContent);

        // } else {
        //     $lastMethodPattern = '/;\s*(\}\s*\n})$/m';

        //     $pluginDefinition = $this->option('central') ?
        //         $baseIndent . '    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()' . PHP_EOL .
        //         $baseIndent . '        ->centralApp(' . static::getTenantModelClass() . '),' :
        //         $baseIndent . '    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),';

        //     $pluginsContent = PHP_EOL . $baseIndent . '->plugins([' . PHP_EOL .
        //         $pluginDefinition . PHP_EOL .
        //         $baseIndent . ']); ' . PHP_EOL . str_repeat(' ', 4);

        //     $updatedContent = preg_replace($lastMethodPattern, $pluginsContent . '$1', $fileContent, 1);

        //     file_put_contents($panelPath, $updatedContent);
        // }


    // $fileContent = file_get_contents($panelPath);

    // preg_match('/^(\s*)->/m', $fileContent, $indentMatch);

    // $baseIndent = $indentMatch[1] ?? '';

    // // Check for existing plugins
    // $pluginDefinition = $this->option('central') ?
    //     $baseIndent . '    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make()' . PHP_EOL .
    //     $baseIndent . '        ->centralApp(' . static::getTenantModelClass() . '),' :
    //     $baseIndent . '    \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),';

    // if (preg_match('/FilamentShieldPlugin::make\(\)(?:\s*->\s*\w+\(\))*/', $fileContent)) {
    //     $this->components->info('Shield plugin is already installed. Installing the middleware...');
    // } else {
    //     // Handle plugins
    //     $pluginsPattern = '/(->plugins\(\[)([\s\S]*?)(\]\))/';

    //     if (preg_match($pluginsPattern, $fileContent, $matches)) {
    //         // Add the new plugin to the existing plugins
    //         $newPluginsContent = '->plugins([' . PHP_EOL .
    //             $pluginDefinition . PHP_EOL .
    //             $baseIndent . str_repeat(' ', 4) .
    //             trim($matches[2]) . '])';

    //         $fileContent = preg_replace($pluginsPattern, $newPluginsContent, $fileContent);
    //     } else {
    //         $lastMethodPattern = '/;\s*(\}\s*\n})$/m';

    //         // Create the whole plugins block
    //         $pluginsContent = PHP_EOL . $baseIndent . '->plugins([' . PHP_EOL .
    //             $pluginDefinition . PHP_EOL .
    //             $baseIndent . ']); ' . PHP_EOL . str_repeat(' ', 4);

    //         $fileContent = preg_replace($lastMethodPattern, $pluginsContent . '$1', $fileContent, 1);
    //     }
    // }

    // // Check for existing tenantMiddleware
    // $middlewarePattern = '/(->tenantMiddleware\(\[)([\s\S]*?)(\],\s*isPersistent:\s*true\))/';

    // // Handle tenantMiddleware
    // if ($panel->hasTenancy()) {
    //     if (preg_match($middlewarePattern, $fileContent, $matches)) {
    //         // Check if SetCurrentTenantForShield::class already exists
    //         if (strpos($matches[2], 'SetCurrentTenantForShield::class') === false) {
    //             // Add class to existing tenantMiddleware
    //             $newMiddlewareContent = '->tenantMiddleware([' . PHP_EOL .
    //                 $baseIndent . str_repeat(' ', 4) .
    //                 'SetCurrentTenantForShield::class,' . PHP_EOL .
    //                 trim($matches[2]) .
    //                 $baseIndent . '], isPersistent: true)';

    //             $fileContent = preg_replace($middlewarePattern, $newMiddlewareContent, $fileContent);
    //         } else {
    //             $this->components->warn('SetCurrentTenantForShield is already in the tenant middleware!');
    //         }
    //     } else {
    //         $lastMethodPattern = '/;\s*(\}\s*\n})$/m';

    //         // Create the whole tenantMiddleware block
    //         $middlewareContent = PHP_EOL . $baseIndent . '->tenantMiddleware([' . PHP_EOL .
    //             $baseIndent . str_repeat(' ', 4) .
    //             'SetCurrentTenantForShield::class,' . PHP_EOL .
    //             $baseIndent . '], isPersistent: true); ' . PHP_EOL . str_repeat(' ', 4);

    //         $fileContent = preg_replace($lastMethodPattern, $middlewareContent . '$1', $fileContent, 1);
    //     }
    // }
    // // Write back the updated content to the file
    // file_put_contents($panelPath, $fileContent);

        $this->info('Shield has been successfully installed!');

        return static::SUCCESS;
    }

    protected static function getPanelWithTenancySupport(): ?Panel
    {
        return collect(Filament::getPanels())
            ->first(fn (Panel $panel): bool => $panel->hasTenancy());
    }

    protected static function getTenantModelClass(): string
    {
        return str(static::getPanelWithTenancySupport()?->getTenantModel())
            ->prepend('\\')
            ->append('::class')
            ->toString();
    }
}
