<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use Closure;

trait CanBeCentralApp
{
    protected bool | Closure $isCentralApp = false;

    public function centralApp(bool | Closure $condition = true): static
    {
        $this->isCentralApp = $condition;

        return $this;
    }

    public function isCentralApp(): bool
    {
        return (bool) $this->evaluate($this->isCentralApp);
    }
}
