<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Support;

use Illuminate\Support\Fluent;

class ShieldConfig extends Fluent
{
    protected static ?self $instance = null;

    public function __construct(array $attributes = [])
    {
        foreach ($attributes as $key => $value) {
            if (is_array($value)) {
                if ($value === []) {
                    // Empty arrays behave like "not set"
                    $this->attributes[$key] = [];

                    continue;
                }

                $isAssoc = array_keys($value) !== range(0, count($value) - 1);

                if ($isAssoc) {
                    // Assoc arrays → treat as option bags
                    $this->attributes[$key] = new self($value);
                } else {
                    // Sequential arrays → keep as is
                    $this->attributes[$key] = $value;
                }
            } else {
                $this->attributes[$key] = $value;
            }
        }
    }

    public static function __callStatic(mixed $name, mixed $arguments)
    {
        $instance = static::init();

        // If the key exists as an attribute, return it
        if (array_key_exists($name, $instance->attributes)) {
            return $instance->attributes[$name];
        }

        // Otherwise fallback to Fluent’s magic
        return $instance->$name(...$arguments)->isNotEmpty() ?: null;
    }

    // we don't want to memoize this, because we want it to react to config changes at runtime
    public static function init(): self
    {
        return static::$instance = new self(config('filament-shield'));
    }

    public function rolesPanelPrefixEnabled(): bool
    {
        return (bool) ($this->roles->panel_prefix ?? false);
    }

    public function permissionsPanelPrefixEnabled(): bool
    {
        return (bool) ($this->permissions->panel_prefix ?? false);
    }

    public function policiesPanelPathEnabled(): bool
    {
        return (bool) ($this->policies->panel_path ?? false);
    }

    public function policiesPanelAwareResolutionEnabled(): bool
    {
        return (bool) ($this->policies->panel_aware_resolution ?? false);
    }

    public function policiesForcePathEnabled(): bool
    {
        return (bool) ($this->policies->force_path ?? false);
    }

    public function permissionsSeparator(): string
    {
        return (string) ($this->permissions->separator ?? ':');
    }

    public function permissionsPanelPrefixSeparator(): string
    {
        return (string) ($this->permissions->panel_prefix_separator ?? $this->permissionsSeparator());
    }

    public function rolesPanelPrefixSeparator(): string
    {
        return (string) ($this->roles->panel_prefix_separator ?? $this->permissionsPanelPrefixSeparator());
    }
}
