<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanMakePanelTenantable;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Commands\Concerns\CanRegisterPlugin;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Process;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

#[AsCommand(name: 'shield:install', description: 'Install and configure shield for the given Filament Panel')]
class InstallCommand extends Command implements PromptsForMissingInput
{
    use CanMakePanelTenantable;
    use CanManipulateFiles;
    use CanRegisterPlugin;
    use Prohibitable;

    /** @var string */
    protected $signature = 'shield:install {panel?} {--tenant}';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        if ($this->option('tenant') && ! Utils::isTenancyEnabled()) {
            $this->components->error('Shield is not configured with tenancy/teams feature.');
            if (! confirm('Would you like to proceed with normal installation?', true)) {
                return Command::FAILURE;
            }
        }

        $shouldSetPanelAsCentralApp = false;

        $panelId = $this->argument('panel') ?: select(
            label: 'Which Panel would you like to install Shield for?',
            options: collect(Filament::getPanels())->keys(),
            required: true
        );

        $panel = Filament::getPanel($panelId);

        $tenant = $this->option('tenant') ? config()->get('filament-shield.tenant_model') : null;

        $tenantModelClass = str($tenant)
            ->prepend('\\')
            ->append('::class')
            ->toString();

        $panelPath = app_path(
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
        if (Utils::isTenancyEnabled() && filled($tenant) && ! $shouldSetPanelAsCentralApp) {
            $this->makePanelTenantable(
                panel: $panel,
                panelPath: $panelPath,
                tenantModelClass: $tenantModelClass
            );
        }

        Process::run("php artisan shield:generate --resource=RoleResource --panel={$panel->getId()}");

        return Command::SUCCESS;
    }
}
