<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

trait CanCustomizeLabels
{
    protected bool $prettyResourcePermissionsLabel = true;
    protected bool $prettyPagePermissionsLabel = true;
    protected bool $prettyWidgetPermissionsLabel = true;
    protected bool $prettyCustomPermissionsLabel = true;

    public function prettyResourcePermissionsLabel(bool $bool = true): static
    {
        $this->prettyResourcePermissionsLabel = $bool;

        return $this;
    }

    public function isPrettyResourcePermissionsLabel(): bool
    {
        return  $this->prettyResourcePermissionsLabel;
    }

    public function prettyPagePermissionsLabel(bool $bool = true): static
    {
        $this->prettyPagePermissionsLabel = $bool;

        return $this;
    }

    public function isPrettyPagePermissionsLabel(): bool
    {
        return  $this->prettyPagePermissionsLabel;
    }

    public function prettyWidgetPermissionsLabel(bool $bool = true): static
    {
        $this->prettyWidgetPermissionsLabel = $bool;

        return $this;
    }

    public function isPrettyWidgetPermissionsLabel(): bool
    {
        return  $this->prettyWidgetPermissionsLabel;
    }

    public function prettyCustomPermissionsLabel(bool $bool = true): static
    {
        $this->prettyCustomPermissionsLabel = $bool;

        return $this;
    }

    public function isPrettyCustomPermissionsLabel(): bool
    {
        return  $this->prettyCustomPermissionsLabel;
    }

}