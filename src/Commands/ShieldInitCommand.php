<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'shield:init', description: 'Setup core package requirements and initialize Shield')]
class ShieldInitCommand extends Command
{
    use Concerns\CanGenerateRelationshipsForTenancy;
    use Concerns\CanMakePanelTenantable;
    use Concerns\CanManipulateFiles;
    use Concerns\CanRegisterPlugin;

    /** @var string */
    protected $signature = 'shield:init {--panel=} {--central} {--tenant=} {--generate}';

    /** @var string */
    protected $description = 'Setup core package requirements and initialize Shield';

    public function handle(): int
    {
        $shouldSetPanelAsCentralApp = $this->option('central');

        $panel = Filament::getPanel($this->option('panel') ?? null);

        if ($panel->hasTenancy() && $shouldSetPanelAsCentralApp) {
            $this->components->warn('Cannot install Shield as `Central` on a tenant panel!');

            return static::FAILURE;
        }

        if (! $panel->hasTenancy() && $shouldSetPanelAsCentralApp && blank(static::getPanelWithTenancySupport())) {
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

        // Handle Shield plugin registration
        $this->registerPlugin(
            panelPath: $panelPath,
            centralApp: $shouldSetPanelAsCentralApp,
            modelOfPanelWithTenancy: static::getTenantModelClass()
        );

        // Handle Shield tenancy configuration
        $this->makePanelTenantable(
            panel: $panel,
            panelPath: $panelPath,
            tenantModel: $this->option('tenant') ?? null
        );

        // Handle Relationships generation for tenant and resources' models
        if ($this->option('generate')) {
            $this->generateRelationships($panel);
        }

        $this->components->info('Shield has been successfully configured & registered!');

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
