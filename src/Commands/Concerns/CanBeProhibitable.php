<?php

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

// Introduced in Laravel 11.9 should be removed when support for Laravel 10 is dropped
trait CanBeProhibitable
{
    /**
     * Indicates if the command should be prohibited from running.
     *
     * @var bool
     */
    protected static $prohibitedFromRunning = false;

    /**
     * Indicate whether the command should be prohibited from running.
     *
     * @param  bool  $prohibit
     * @return void
     */
    public static function prohibit($prohibit = true)
    {
        static::$prohibitedFromRunning = $prohibit;
    }

    /**
     * Determine if the command is prohibited from running and display a warning if so.
     *
     * @return bool
     */
    protected function isProhibited(bool $quiet = false)
    {
        if (! static::$prohibitedFromRunning) {
            return false;
        }

        if (! $quiet) {
            $this->components->warn('This command is prohibited from running in this environment.');
        }

        return true;
    }
}
