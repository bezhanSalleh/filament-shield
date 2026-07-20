<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Tests\Fixtures;

class PluginProvidedPolicy
{
    public function viewAny(): bool
    {
        return true;
    }
}
