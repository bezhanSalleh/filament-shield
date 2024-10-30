<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Panel;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Attribute\AsCommand;
use Illuminate\Contracts\Console\PromptsForMissingInput;

#[AsCommand(name: 'shield:init', description: 'Setup core package requirements and initialize Shield')]
class ShieldInitCommand extends Command implements PromptsForMissingInput
{
    use Concerns\CanGenerateRelationshipsForTenancy;
    use Concerns\CanMakePanelTenantable;
    use Concerns\CanManipulateFiles;
    use Concerns\CanRegisterPlugin;

    /** @var string */
    protected $signature = 'shield:init {panel} {--central} {--tenant=} {--generate}';

    /** @var string */
    protected $description = 'Setup core package requirements and initialize Shield';

    public function handle(): int
    {
        $shouldSetPanelAsCentralApp = $this->option('central') ?? false;

        $panel = Filament::getPanel($this->argument('panel') ?? null);

        $tenantModel = $this->option('tenant') ?? null;

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

        $tenantModelClass = str($tenantModel)->contains('\\')
            ? $tenantModel
            : str($tenantModel)->prepend('App\\Models\\')
                ->toString();

        if (filled($tenantModel) && ! class_exists($tenantModelClass) && ! $tenantModelClass instanceof Model) {
            $this->components->error("Tenant model not found: {$tenantModel}");

            return Command::FAILURE;
        }

        if ($panel->hasTenancy() && $shouldSetPanelAsCentralApp) {
            $this->components->warn('Cannot install Shield as `Central App` on a tenant panel!');
            return static::FAILURE;
        }

        if (! $panel->hasTenancy() && $shouldSetPanelAsCentralApp && blank($tenantModelClass)) {
            $this->components->warn('Please provide a valid tenant `Model`!');
            return static::INVALID;
        }

        // Handle Shield plugin registration
        $this->registerPlugin(
            panelPath: $panelPath,
            centralApp: $shouldSetPanelAsCentralApp && ! $panel->hasTenancy(),
            tenantModelClass: $tenantModelClass
        );

        // Handle Shield tenancy configuration
        $this->makePanelTenantable(
            panel: $panel,
            panelPath: $panelPath,
            tenantModel: $tenantModel
        );

        // Handle Relationships generation for tenant and resources' models
        if ($this->option('generate')) {
            $this->generateRelationships($panel);
        }

        $this->components->info('Shield has been successfully configured & registered!');

        return static::SUCCESS;
    }
}
