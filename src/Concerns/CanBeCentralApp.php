<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

use Closure;

trait CanBeCentralApp
{
    protected bool | Closure $isCentralApp = false;

    protected Closure | string | null $tenantModel = null;

    public function centralApp(string $model, bool | Closure $condition = true): static
    {
        $this->tenantModel = $model;

        $this->isCentralApp = $condition;

        return $this;
    }

    public function isCentralApp(): bool
    {
        return (bool) $this->evaluate($this->isCentralApp);
    }

    public function getTenantModel(): ?string
    {
        return $this->evaluate($this->tenantModel);
    }
}
