<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

trait CanGetBasePath
{
    protected function getBasePath(): string
    {
        return app_path((string) str('Filament\\Resources\\Shield')->replace('\\', '/'));
    }
}
