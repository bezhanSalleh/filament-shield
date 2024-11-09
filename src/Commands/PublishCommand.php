<?php

namespace BezhanSalleh\FilamentShield\Commands;

use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;

#[AsCommand(name: 'shield:publish', description: "Publish Shield's Resource.")]
class PublishCommand extends Command
{
    use Concerns\CanBeProhibitable;
    use Concerns\CanManipulateFiles;

    protected $signature = 'shield:publish {panel}';

    public function handle(Filesystem $filesystem): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        Filament::setCurrentPanel(Filament::getPanel($this->argument('panel')));

        $panel = Filament::getCurrentPanel();

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

        $roleResourcePath = str('\\RoleResource.php')
            ->prepend($newResourcePath)
            ->replace('\\', '/')
            ->toString();

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = confirm('Shield Resource already exists. Overwrite?');
            if (! $confirmed) {
                return Command::INVALID;
            }
        }

        $filesystem->ensureDirectoryExists($newResourcePath);
        $filesystem->copyDirectory(__DIR__ . '/../Resources', $newResourcePath);

        $currentNamespace = 'BezhanSalleh\\FilamentShield\\Resources';

        $this->replaceInFile($roleResourcePath, $currentNamespace, $newResourceNamespace);
        $this->replaceInFile($newResourcePath . '/RoleResource/Pages/CreateRole.php', $currentNamespace, $newResourceNamespace);
        $this->replaceInFile($newResourcePath . '/RoleResource/Pages/EditRole.php', $currentNamespace, $newResourceNamespace);
        $this->replaceInFile($newResourcePath . '/RoleResource/Pages/ViewRole.php', $currentNamespace, $newResourceNamespace);
        $this->replaceInFile($newResourcePath . '/RoleResource/Pages/ListRoles.php', $currentNamespace, $newResourceNamespace);

        $this->components->info("Shield's Resource have been published successfully!");

        return Command::SUCCESS;
    }
}
