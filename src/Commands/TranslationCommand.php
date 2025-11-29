<?php

declare(strict_types=1);

namespace BezhanSalleh\FilamentShield\Commands;

use BezhanSalleh\FilamentShield\Commands\Concerns\CanManipulateFiles;
use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Facades\Filament;
use Illuminate\Console\Command;
use Illuminate\Console\Prohibitable;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

#[AsCommand(name: 'shield:translation', description: 'Generate a translation file for permission labels for the given locale.')]
class TranslationCommand extends Command implements PromptsForMissingInput
{
    use CanManipulateFiles;
    use Prohibitable;

    /** @var string */
    public $signature = 'shield:translation
        {locale : The locale to generate the file for}
        {--panel= : Panel ID to get the permissions from}
        {--path= : Custom path for the translations file}
    ';

    public function handle(): int
    {
        if ($this->isProhibited()) {
            return Command::FAILURE;
        }

        $panel = $this->option('panel') ?: select(
            label: 'Which panel do you want to generate permission translations for?',
            options: collect(Filament::getPanels())->keys()->toArray()
        );

        Filament::setCurrentPanel(Filament::getPanel($panel));

        $locale = $this->argument('locale');
        $localizationKey = Utils::getConfig()->localization->key;

        $defaultFilename = Str::of($localizationKey)->afterLast('::')->toString();
        $defaultPath = lang_path(sprintf('%s/%s.php', $locale, $defaultFilename));

        $path = $this->option('path') ?: text(
            label: 'Where would you like to save the translations file?',
            default: $defaultPath
        );

        $translations = $this->gatherPermissionLabels();

        if ($this->checkForCollision([$path])) {
            $confirmed = confirm('The file already exists. Do you want to overwrite it?', default: false);
            if (! $confirmed) {
                return Command::FAILURE;
            }
        }

        $this->writeTranslationsFile($path, $translations);

        $this->components->info('Translations file generated at: ' . $path);

        return Command::SUCCESS;
    }

    protected function gatherPermissionLabels(): array
    {
        $translations = [];

        // Resource permission affixes (view, view_any, create, update, delete, etc.)
        $this->gatherAffixLabels($translations);

        // Page permissions (snake_case of permission key)
        $this->gatherPageLabels($translations);

        // Widget permissions (snake_case of permission key)
        $this->gatherWidgetLabels($translations);

        // Custom permissions (snake_case of permission key)
        $this->gatherCustomPermissionLabels($translations);

        ksort($translations);

        return $translations;
    }

    protected function gatherAffixLabels(array &$translations): void
    {
        $resources = FilamentShield::getResources() ?? [];

        $affixes = collect($resources)
            ->flatMap(fn (array $resource): array => array_keys($resource['permissions']))
            ->unique()
            ->values()
            ->toArray();

        foreach ($affixes as $affix) {
            $localizationKey = Utils::toLocalizationKey($affix);
            $translations[$localizationKey] = FilamentShield::getAffixLabel($affix);
        }
    }

    protected function gatherPageLabels(array &$translations): void
    {
        $pages = FilamentShield::getPages() ?? [];

        foreach ($pages as $page) {
            foreach ($page['permissions'] as $key => $label) {
                $localizationKey = Utils::toLocalizationKey($key);
                $translations[$localizationKey] = $label;
            }
        }
    }

    protected function gatherWidgetLabels(array &$translations): void
    {
        $widgets = FilamentShield::getWidgets() ?? [];

        foreach ($widgets as $widget) {
            foreach ($widget['permissions'] as $key => $label) {
                $localizationKey = Utils::toLocalizationKey($key);
                $translations[$localizationKey] = $label;
            }
        }
    }

    protected function gatherCustomPermissionLabels(array &$translations): void
    {
        $customPermissions = FilamentShield::getCustomPermissions() ?? [];

        foreach ($customPermissions as $key => $label) {
            $localizationKey = Utils::toLocalizationKey($key);
            $translations[$localizationKey] = $label;
        }
    }

    protected function writeTranslationsFile(string $path, array $translations): void
    {
        $content = "<?php\n\n";
        $content .= "/**\n";
        $content .= " * Shield Permission Labels\n";
        $content .= " *\n";
        $content .= " * Translate the values below to localize permission labels in your application.\n";
        $content .= " */\n\n";
        $content .= "return [\n";

        foreach ($translations as $key => $label) {
            $escapedKey = addslashes((string) $key);
            $escapedLabel = addslashes((string) $label);
            $content .= "    '{$escapedKey}' => '{$escapedLabel}',\n";
        }

        $content .= "];\n";

        $this->writeFile($path, $content);
    }
}
