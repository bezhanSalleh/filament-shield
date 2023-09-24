<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Concerns;

trait CanCustomizeColumns
{
    protected int | string | array $checkboxListColumnSpan = 'full';

    protected int | string | array $checkboxListColumns = [
        'sm' => 2,
        'lg' => 4,
    ];

    protected int | string | array $gridColumns = 1;

    protected int | string | array $resourceCheckboxListColumnSpan = 'full';

    protected int | string | array $resourceCheckboxListColumns = [
        'sm' => 2,
        'lg' => 4,
    ];

    protected int | string | array $sectionColumnSpan = 'full';

    protected int | string | array $sectionColumns = [
        'sm' => 2,
        'lg' => 4,
    ];

    public function checkboxListColumns(int | string | array $columns): static
    {
        $this->checkboxListColumns = $columns;

        return $this;
    }

    public function checkboxListColumnSpan(int | string | array $columnSpan): static
    {
        $this->checkboxListColumnSpan = $columnSpan;

        return $this;
    }

    public function gridColumns(int | string | array $columns): static
    {
        $this->gridColumns = $columns;

        return $this;
    }

    public function resourceCheckboxListColumns(int | string | array $columns): static
    {
        $this->resourceCheckboxListColumns = $columns;

        return $this;
    }

    public function resourceCheckboxListColumnSpan(int | string | array $columnSpan): static
    {
        $this->resourceCheckboxListColumnSpan = $columnSpan;

        return $this;
    }

    public function sectionColumns(int | string | array $columns): static
    {
        $this->sectionColumns = $columns;

        return $this;
    }

    public function sectionColumnSpan(int | string | array $columnSpan): static
    {
        $this->sectionColumnSpan = $columnSpan;

        return $this;
    }

    public function getCheckboxListColumns(): int | string | array
    {
        return $this->checkboxListColumns;
    }

    public function getCheckboxListColumnSpan(): int | string | array
    {
        return $this->checkboxListColumnSpan;
    }

    public function getGridColumns(): int | string | array
    {
        return $this->gridColumns;
    }

    public function getResourceCheckboxListColumns(): int | string | array
    {
        return $this->resourceCheckboxListColumns;
    }

    public function getResourceCheckboxListColumnSpan(): int | string | array
    {
        return $this->resourceCheckboxListColumnSpan;
    }

    public function getSectionColumns(): int | string | array
    {
        return $this->sectionColumns;
    }

    public function getSectionColumnSpan(): int | string | array
    {
        return $this->sectionColumnSpan;
    }
}
