<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use BezhanSalleh\FilamentShield\Support\ShieldConfig;

trait HasConfig
{
    protected ?ShieldConfig $config = null;

    public function getConfig(): ShieldConfig
    {
        return $this->config ??= ShieldConfig::init();
    }
}
