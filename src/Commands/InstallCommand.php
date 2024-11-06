<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;

#[AsCommand(name: 'shield:install')]
class InstallCommand extends Command implements PromptsForMissingInput
{
    use Concerns\CanGenerateRelationshipsForTenancy;
    use Concerns\CanMakePanelTenantable;
    use Concerns\CanManipulateFiles;
    use Concerns\CanRegisterPlugin;

    /** @var string */
    protected $signature = 'shield:install {panel} {--central} {--tenant} {--generate}';

    /** @var string */
    protected $description = 'Install and configure shield for the given Filament Panel';

    public function handle(): int
    {
        $shouldSetPanelAsCentralApp = $this->option('central') ?? false;

        $panel = Filament::getPanel($this->argument('panel') ?? null);

        $tenant = $this->option('tenant') ? config()->get('filament-shield.tenant_model') : null;

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

        if ($panel->hasTenancy() && $shouldSetPanelAsCentralApp) {
            $this->components->warn('Cannot install Shield as `Central App` on a tenant panel!');
            return static::FAILURE;
        }

        if (! $panel->hasTenancy() && $shouldSetPanelAsCentralApp && blank($tenant)) {
            $this->components->warn('Make sure you have at least a panel with tenancy setup first!');
            return static::INVALID;
        }

        $this->registerPlugin(
            panelPath: $panelPath,
            centralApp: $shouldSetPanelAsCentralApp && ! $panel->hasTenancy(),
            tenantModelClass: $tenant
        );

        if (filled($tenant)) {
            $this->makePanelTenantable(
                panel: $panel,
                panelPath: $panelPath,
                tenantModel: $tenant
            );
        }

        if ($this->option('generate')) {
            $this->generateRelationships($panel);
        }

        $this->components->info('Shield has been successfully configured & installed!');

        return static::SUCCESS;
    }
}
