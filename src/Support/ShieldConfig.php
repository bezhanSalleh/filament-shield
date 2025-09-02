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

    // we don't want to memoize this, because we want it to react to config changes at runtime
    public static function init(): self
    {
        return static::$instance = new self(config('filament-shield'));
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
}
