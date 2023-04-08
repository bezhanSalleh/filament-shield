<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands\Concerns;

use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Process\Process as SymfonyProcess;

trait CanRunShellCommands
{
    use InteractsWithIO;

    /**
     * Installs the given Composer Packages into the application.
     */
    protected function requireComposerPackages(array $packages)
    {
        $this->runProcess(array_merge(
            ['composer', 'require'],
            $packages
        ));
    }

    /**
     * Removes the given Composer Packages from the application.
     */
    protected function removeComposerPackages(array $packages)
    {
        $this->runProcess(array_merge(
            ['composer', 'remove'],
            $packages
        ));
    }

    protected function runProcess(array $command)
    {
        $process = new SymfonyProcess($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1', 'COMPOSER_DISABLE_NETWORK' => '1'], null, null);
        $process->setTimeout(null);
        // $process->run();
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if (! $process->isSuccessful()) {
            $this->components->error("Error executing command: {$process->getErrorOutput()}");
            exit(1);
        }
    }
}
