<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'shield:install')]
class InstallCommand extends Command implements PromptsForMissingInput
{
    use Concerns\CanBeProhibitable;
    use Concerns\CanMakePanelTenantable;
    use Concerns\CanManipulateFiles;
    use Concerns\CanRegisterPlugin;

    /** @var string */
    protected $signature = 'shield:install {panel}
     {--tenant}
     {--panel-provider-path= : Filament provider path is determined according to the app base path.}';


    /** @var string */
    protected $description = 'Install and configure shield for the given Filament Panel';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $shouldSetPanelAsCentralApp = false;

        $panel = Filament::getPanel($this->argument('panel') ?? null);

        $tenant = $this->option('tenant') ? config()->get('filament-shield.tenant_model') : null;
        $panelProviderPath=$this->option('panel-provider-path');
        $tenantModelClass = str($tenant)
            ->prepend('\\')
            ->append('::class')
            ->toString();

        $panelPath = !empty($panelProviderPath)?
            base_path($panelProviderPath):
            app_path(
            (string) str($panel->getId())
                ->studly()
                ->append('PanelProvider')
                ->prepend('Providers' . DIRECTORY_SEPARATOR . 'Filament' . DIRECTORY_SEPARATOR)
                ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
                ->append('.php'),
        );

        if (! $this->fileExists($panelPath)) {
            $this->error("Panel not found: {$panelPath}");

            return Command::FAILURE;
        }

        // if ($panel->hasTenancy() && $shouldSetPanelAsCentralApp) {
        //     $this->components->warn('Cannot install Shield as `Central App` on a tenant panel!');
        //     return Command::FAILURE;
        // }

        // if (! $panel->hasTenancy() && $shouldSetPanelAsCentralApp && blank($tenant)) {
        //     $this->components->warn('Make sure you have at least a panel with tenancy setup first!');
        //     return Command::INVALID;
        // }

        $this->registerPlugin(
            panelPath: $panelPath,
            /** @phpstan-ignore-next-line */
            centralApp: $shouldSetPanelAsCentralApp && ! $panel->hasTenancy(),
            tenantModelClass: $tenantModelClass
        );

        /** @phpstan-ignore-next-line */
        if (filled($tenant) && ! $shouldSetPanelAsCentralApp) {
            $this->makePanelTenantable(
                panel: $panel,
                panelPath: $panelPath,
                tenantModelClass: $tenantModelClass
            );
        }

        Process::run("php artisan shield:generate --resource=RoleResource --panel={$panel->getId()}");

        $this->components->info('Shield has been successfully configured & installed!');

        return Command::SUCCESS;
    }
}
