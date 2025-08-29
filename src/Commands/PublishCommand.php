<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanAskForResource;
use Filament\Support\Commands\Concerns\HasCluster;
use Filament\Support\Commands\Concerns\HasPanel;
use Filament\Support\Commands\Concerns\HasResourcesLocation;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

use function Laravel\Prompts\confirm;

#[AsCommand(name: 'shield:publish', description: "Publish Shield's Resource.")]
class PublishCommand extends Command
{
    use CanAskForResource;
    use CanManipulateFiles;
    use HasCluster;
    use HasPanel;
    use HasResourcesLocation;
    use Prohibitable;

    protected bool $isNested;

    /**
     * @var ?class-string
     */
    protected ?string $parentResourceFqn = null;

    public function handle(Filesystem $filesystem): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $this->configurePanel(question: 'Which panel would you like to publish the shield resource in?');
        $this->configureIsNested();
        $this->configureCluster();
        $this->configureResourcesLocation(question: 'Which namespace would you like to publish the shield resource in?');
        $this->configureParentResource();

        $roleResourcePath = str(DIRECTORY_SEPARATOR . 'Roles/RoleResource.php')
            ->prepend($this->resourcesDirectory)
            ->replace(['\\', '/'], DIRECTORY_SEPARATOR)
            ->toString();

        if ($this->checkForCollision([$roleResourcePath])) {
            $confirmed = confirm('Shield Resource already exists. Overwrite?');
            if (! $confirmed) {
                return Command::INVALID;
            }
        }

        $resourcePath = $this->resourcesDirectory . '/Roles';
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
            'BezhanSalleh\\FilamentShield\\Resources\\Roles',
            $this->resourcesNamespace . '\\Roles'
        );

        foreach (['CreateRole', 'EditRole', 'ListRoles', 'ViewRole'] as $page) {
            $this->replaceInFile(
                $resourcePath . "/Pages/{$page}.php",
                'BezhanSalleh\\FilamentShield\\Resources\\Roles',
                $this->resourcesNamespace . '\\Roles'
            );
        }

        $this->components->info("Shield's Resource have been published successfully!");

        return Command::SUCCESS;
    }

    /**
     * @return array<InputOption>
     */
    protected function getOptions(): array
    {
        return [
            new InputOption(
                name: 'cluster',
                shortcut: 'C',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'The cluster to publish the resource in',
            ),
            new InputOption(
                name: 'nested',
                shortcut: 'N',
                mode: InputOption::VALUE_OPTIONAL,
                description: 'Nest the resource inside another through a relationship',
                default: false,
            ),

            new InputOption(
                name: 'panel',
                shortcut: null,
                mode: InputOption::VALUE_REQUIRED,
                description: 'The panel to publish the resource in',
            ),
            new InputOption(
                name: 'force',
                shortcut: 'F',
                mode: InputOption::VALUE_NONE,
                description: 'Overwrite the contents of the files if they already exist',
            ),
        ];
    }

    protected function configureIsNested(): void
    {
        $this->isNested = $this->option('nested') !== false;
    }

    protected function configureCluster(): void
    {
        if ($this->isNested) {
            $this->configureClusterFqn(
                initialQuestion: 'Is the parent resource in a cluster?',
                question: 'Which cluster is the parent resource in?',
            );
        } else {
            $this->configureClusterFqn(
                initialQuestion: 'Would you like to create this resource in a cluster?',
                question: 'Which cluster would you like to create this resource in?',
            );
        }

        if (blank($this->clusterFqn)) {
            return;
        }

        $this->configureClusterResourcesLocation();
    }

    protected function configureParentResource(): void
    {
        if (! $this->isNested) {
            return;
        }

        $this->parentResourceFqn = $this->askForResource(
            question: 'Which resource would you like to nest this resource inside?',
            initialResource: $this->option('nested'),
        );

        $pluralParentResourceBasenameBeforeResource = (string) str($this->parentResourceFqn)
            ->classBasename()
            ->beforeLast('Resource')
            ->plural();

        $parentResourceNamespacePartBeforeBasename = (string) str($this->parentResourceFqn)
            ->beforeLast('\\')
            ->classBasename();

        if ($pluralParentResourceBasenameBeforeResource === $parentResourceNamespacePartBeforeBasename) {
            $this->resourcesNamespace = (string) str($this->parentResourceFqn)
                ->beforeLast('\\')
                ->append('\\Resources');
            $this->resourcesDirectory = (string) str((new \ReflectionClass($this->parentResourceFqn))->getFileName())
                ->beforeLast(DIRECTORY_SEPARATOR)
                ->append('/Resources');

            return;
        }

        $this->resourcesNamespace = "{$this->parentResourceFqn}\\Resources";
        $this->resourcesDirectory = (string) str((new \ReflectionClass($this->parentResourceFqn))->getFileName())
            ->beforeLast('.')
            ->append('/Resources');
    }
}
