<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use Filament\Facades\Filament;
use Illuminate\Support\Collection;

trait HasEntityDiscovery
{
    public function discoverEntities(string $entityType): ?Collection
    {
        return match ($entityType) {
            'resources' => $this->discoverResources(),
            'pages' => $this->discoverPages(),
            'widgets' => $this->discoverWidgets(),
            default => null,
        };
    }

    public function discoverResources(): Collection
    {
        return $this->getConfig()->discovery->discover_all_resources
            ? collect(Filament::getPanels())->flatMap(fn ($panel): array => $panel->getResources())->unique()
            : collect(Filament::getResources());
    }

    public function discoverPages(): Collection
    {
        return $this->getConfig()->discovery->discover_all_pages
            ? collect(Filament::getPanels())->flatMap(fn ($panel): array => $panel->getPages())->unique()
            : collect(Filament::getPages());
    }

    public function discoverWidgets(): Collection
    {
        return $this->getConfig()->discovery->discover_all_widgets
            ? collect(Filament::getPanels())->flatMap(fn ($panel): array => $panel->getWidgets())->unique()
            : collect(Filament::getWidgets());
    }
}
