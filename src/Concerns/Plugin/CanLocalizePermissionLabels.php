<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns\Plugin;

trait CanLocalizePermissionLabels
{
    protected bool $arePermissionLabelsLocalized = true;

    public function localizePermissionLabels(bool $condition = true): static
    {
        $this->arePermissionLabelsLocalized = $condition;

        return $this;
    }

    public function hasLocalizedPermissionLabels(): bool
    {
        return $this->arePermissionLabelsLocalized;
    }
}
