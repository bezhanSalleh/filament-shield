<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

trait HasSimpleResourcePermissionView
{
    protected bool $isSimple = false;

    public function simpleResourcePermissionView(bool $condition = true): static
    {
        $this->isSimple = $condition;

        return $this;
    }

    public function hasSimpleResourcePermissionView(): bool
    {
        return $this->isSimple;
    }
}
