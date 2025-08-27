<?php

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

#[AsCommand(name: 'shield:publish', description: "Publish Shield's Resource.")]
class PublishCommand extends Command
{
    use Prohibitable;
    use CanManipulateFiles;

    public function handle(Filesystem $filesystem): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        Filament::setCurrentPanel(Filament::getPanel($this->argument('panel')));

        $panel = Filament::getCurrentOrDefaultPanel();

        $resourceDirectories = $panel->getResourceDirectories();
        $resourceNamespaces = $panel->getResourceNamespaces();

        $newResourceNamespace = (count($resourceNamespaces) > 1)
            ? select(
                label: 'Which namespace would you like to publish this in?',
                options: $resourceNamespaces
            )
            : Arr::first($resourceNamespaces);

        $newResourcePath = (count($resourceDirectories) > 1)
            ? $resourceDirectories[array_search($newResourceNamespace, $resourceNamespaces)]
            : Arr::first($resourceDirectories);

        $roleResourcePath = str(DIRECTORY_SEPARATOR . 'Roles/RoleResource.php')
            ->prepend($newResourcePath)
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->toString();

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = confirm('Shield Resource already exists. Overwrite?');
            if (! $confirmed) {
                return Command::INVALID;
            }
        }

        $resourcePath = $newResourcePath . '/Roles';
        $filesystem->ensureDirectoryExists($resourcePath . '/Pages');

        $sourcePath = __DIR__ . '/../Resources/Roles';

        $filesystem->copy(
            $sourcePath . '/RoleResource.php',
            $resourcePath . '/RoleResource.php'
        );

        $filesystem->copyDirectory(
            $sourcePath . '/Pages',
            $resourcePath . '/Pages'
        );

        $this->replaceInFile(
            $resourcePath . '/RoleResource.php',
            'BezhanSalleh\\FilamentShield\\Resources',
            $newResourceNamespace
        );

        foreach (['CreateRole', 'EditRole', 'ListRoles', 'ViewRole'] as $page) {
            $this->replaceInFile(
                $resourcePath . "/Pages/{$page}.php",
                'BezhanSalleh\\FilamentShield\\Resources',
                $newResourceNamespace
            );
        }

        $this->components->info("Shield's Resource have been published successfully!");

        return Command::SUCCESS;
    }
}
