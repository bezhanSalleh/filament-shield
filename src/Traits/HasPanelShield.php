<?php


namespace BezhanSalleh\FilamentShield\Traits;

use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Panel;
use Illuminate\Support\Str;

trait HasPanelShield {
    public function grantedPanelIds(): ?array
        {
            return collect(filament()->getPanels())
                ->filter(
                    function (Panel $panel) {
                        $prepend = Str::of(Utils::getPanelPermissionPrefix())->append('_');
                        $name = Str::of($panel->getId())
                            ->prepend($prepend);
                        return $this->can($name) || $this->hasRole('super_admin');
                    }
                )
                ->reduce(function($panels, Panel $panel) {
                    $panels[] = $panel->getId();
    
                    return $panels;
                });
        }
}