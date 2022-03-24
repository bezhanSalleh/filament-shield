<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use Closure;
use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Artisan;
use Filament\Pages\Actions\ButtonAction;
use League\CommonMark\MarkdownConverter;
use League\CommonMark\CommonMarkConverter;
use Filament\Pages\Contracts\HasFormActions;
use App\Filament\Resources\Shield\RoleResource;
use BezhanSalleh\FilamentShield\Commands\Concerns;
use Filament\Resources\Pages\Concerns\UsesResourceForm;

class ShieldSettings extends Page implements HasFormActions
{
    use Concerns\CanBackupAFile;
    use Concerns\CanManipulateFiles;
    use UsesResourceForm;

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament-shield::pages.shield-settings';

    public function mount(): void
    {
        static::authorizeResourceAccess();

        $this->form->fill([
            'super_admin_role_enabled' => config('filament-shield.super_admin.enabled'),
            'super_admin_role_name' => config('filament-shield.super_admin.role_name'),
            'filament_user_role_enabled' => config('filament-shield.filament_user.enabled'),
            'filament_user_role_name' => config('filament-shield.filament_user.role_name'),
            'resource_permission_prefixes' => config('filament-shield.prefixes.resource'),
            'page_permission_prefix' => config('filament-shield.prefixes.page'),
            'widget_permission_prefix' => config('filament-shield.prefixes.widget'),
            'entities_pages' => config('filament-shield.entities.pages'),
            'entities_widgets' => config('filament-shield.entities.widgets'),
            'entities_resources' => config('filament-shield.entities.resources'),
            'entities_custom_permissions' => config('filament-shield.entities.custom_permissions'),
            'resources_generator_option' => config('filament-shield.resources_generator_option'),
            'exclude_enabled' => config('filament-shield.exclude.enabled'),
            'exclude_pages' => config('filament-shield.exclude.pages'),
            'exclude_widgets' => config('filament-shield.exclude.widgets'),
            'exclude_resources' => config('filament-shield.exclude.resources'),
            'register_role_policy' => config('filament-shield.register_role_policy'),
        ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema($this->getFormSchema())
        ];
    }

    protected function getFormSchema(): array
    {
        $layout = Forms\Components\Card::class;
        return [
            Forms\Components\Grid::make()
                ->schema([
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('super_admin_role_enabled')
                                ->label(__('filament-shield::filament-shield.labels.super_admin.toggle_input'))
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('super_admin_role_name')
                                ->label(__('filament-shield::filament-shield.labels.super_admin.text_input'))
                                ->afterStateHydrated(fn(Closure $set, Closure $get, $state) => $set($state,Str::of($get($state))->snake()))
                                ->visible(fn($get) => $get('super_admin_role_enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),

                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('filament_user_role_enabled')
                                ->label(__('filament-shield::filament-shield.labels.filament_user.toggle_input'))
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('filament_user_role_name')
                                ->label(__('filament-shield::filament-shield.labels.filament_user.text_input'))
                                ->visible(fn($get) => $get('filament_user_role_enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('register_role_policy')
                                ->label(__('filament-shield::filament-shield.labels.role_policy.toggle_input'))
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.yes").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.no").'</span>')
                                ->default(true)
                                ->helperText('<span class="text-md text-gray-600 leading-loose">'.__("filament-shield::filament-shield.labels.role_policy.message").'</span>')
                                ->reactive()
                                ->required()
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                ])
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3
                ]),
            $layout::make()
            ->schema([
                    Forms\Components\Placeholder::make('')
                        ->label(__('filament-shield::filament-shield.labels.prefixes.placeholder')),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TagsInput::make('resource_permission_prefixes')
                                ->label(__('filament-shield::filament-shield.labels.prefixes.resource'))
                                ->placeholder(__('filament-shield::filament-shield.labels.prefixes.resource.placeholder'))
                                ->required()
                                ->separator(','),
                            Forms\Components\TextInput::make('page_permission_prefix')
                                ->label(__('filament-shield::filament-shield.labels.prefixes.page'))
                                ->required(),
                            Forms\Components\TextInput::make('widget_permission_prefix')
                                ->label(__('filament-shield::filament-shield.labels.prefixes.widget'))
                                ->required(),
                        ])
                        ->columns(3)
                ]),

            $layout::make()
                ->schema([
                    Forms\Components\Placeholder::make('Entities\'s Permission Generator Options')
                        ->label(__('filament-shield::filament-shield.labels.entities.placeholder')),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Toggle::make('entities_resources')
                                ->label(__('filament-shield::filament-shield.labels.entities.resources'))
                                ->helperText(fn($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_pages')
                                ->label(__('filament-shield::filament-shield.labels.entities.pages'))
                                ->helperText(fn($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_widgets')
                                ->label(__('filament-shield::filament-shield.labels.entities.widgets'))
                                ->helperText(fn($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_custom_permissions')
                                ->label(__('filament-shield::filament-shield.labels.entities.custom_permissions'))
                                ->helperText(fn($state) => $state ? __("filament-shield::filament-shield.labels.entities.custom_permissions.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.custom_permissions.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive()
                        ])
                        ->columns(4),
                ]),
            $layout::make()
                ->visible(fn($get): bool => (bool) $get('entities_resources'))
                ->schema([
                    Forms\Components\Grid::make()
                    ->schema([
                            Forms\Components\Placeholder::make('')
                                ->content(new HtmlString('<span class="font-medium text-sm text-gray-700">Resources Generator Option</span>')),
                            Forms\Components\Radio::make('resources_generator_option')
                                ->label('')
                                ->options([
                                    'policies_and_permissions' => 'Generate Policies & Permissions',
                                    'policies' => 'Generate only Policies',
                                    'permissions' => 'Generate only Permissions',
                                ])
                                ->inline()
                        ])
                        ->columns(1)
                ]),

            $layout::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Placeholder::make('Exclusion Mode')
                                ->label(__('filament-shield::filament-shield.labels.exclude.placeholder'))
                                ->content(__('filament-shield::filament-shield.labels.exclude.message'))
                                ->extraAttributes(['class' => 'text-sm text-gray-500']),
                            Forms\Components\Toggle::make('exclude_enabled')
                                ->label(fn($state): string => $state ? __("filament-shield::filament-shield.labels.status.enabled") : __("filament-shield::filament-shield.labels.status.disabled"))
                                ->default(config('filament-shield.exclude.enabled'))
                                ->reactive(),
                            Forms\Components\Grid::make()
                                ->visible(fn($get) => $get('exclude_enabled'))
                                ->schema([
                                    Forms\Components\MultiSelect::make('exclude_resources')
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.resources.placeholder"))
                                        ->options(function() {
                                            return collect(Filament::getResources())
                                                ->reduce(function ($resources, $resource) {
                                                    $resources[Str::afterLast($resource, '\\')] = Str::afterLast($resource, '\\');
                                                    return $resources;
                                                }, []);
                                        })
                                        ->label(__("filament-shield::filament-shield.labels.exclude.resources")),
                                    Forms\Components\MultiSelect::make('exclude_pages')
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.pages.placeholder"))
                                        ->options(function () {
                                            return collect(Filament::getPages())
                                                ->reduce(function($pages,$page) {
                                                    $label = Str::of($page)
                                                        ->after('Pages\\')
                                                        ->replace('\\',' ');

                                                    $value = Str::of($page)
                                                        ->after('Pages\\')
                                                        ->replace('\\','');

                                                    $pages["{$value}"] = "{$label}";
                                                    return $pages;
                                            },[]);
                                        })
                                        ->label(__("filament-shield::filament-shield.labels.exclude.pages")),
                                    Forms\Components\MultiSelect::make('exclude_widgets')
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.widgets.placeholder"))
                                        ->options(function () {
                                            return collect(Filament::getWidgets())
                                                ->reduce(function($widgets,$widget) {
                                                    $name = Str::of($widget)
                                                            ->after('Widgets\\')
                                                            ->replace('\\','');
                                                    $widgets["{$name}"] = "{$name}";
                                                    return $widgets;
                                            },[]);
                                        })
                                        ->label(__("filament-shield::filament-shield.labels.exclude.widgets.placeholder")),
                                ])
                                ->columns(3)
                        ])
                ]),
        ];
    }

    protected function generateNewConfig(): void
    {
        $this->copyStubToApp('Config',
            config_path('filament-shield.php'),
            [
                'super_admin_role_enabled' => $this->super_admin_role_enabled ? 'true' : 'false',
                'super_admin_role_name' => $this->super_admin_role_name,
                'filament_user_role_enabled' => $this->filament_user_role_enabled ? 'true' : 'false',
                'filament_user_role_name' => $this->filament_user_role_name,
                'resource_permission_prefixes' => json_encode($this->resource_permission_prefixes),
                'page_permission_prefix' => $this->page_permission_prefix,
                'widget_permission_prefix' => $this->widget_permission_prefix,
                'entities_pages' => $this->entities_pages ? 'true' : 'false',
                'register_role_policy' => $this->register_role_policy ? 'true' : 'false',
                'entities_widgets' => $this->entities_widgets ? 'true' : 'false',
                'entities_resources' => $this->entities_resources ? 'true' : 'false',
                'entities_custom_permissions' => $this->entities_custom_permissions ? 'true' : 'false',
                'resources_generator_option' => $this->resources_generator_option,
                'exclude_enabled' => $this->exclude_enabled ? 'true' : 'false',
                'exclude_pages' => json_encode($this->exclude_pages),
                'exclude_widgets' => json_encode($this->exclude_widgets),
                'exclude_resources' => json_encode($this->exclude_resources),
            ]
        );
    }

    protected function backupCurrentConfig(): void
    {
        $this->isBackupPossible(config_path('filament-shield.php'), config_path('filament-shield.php.bak'));
    }

    public function save(): void
    {
        $this->backupCurrentConfig();

        $this->generateNewConfig();

        Artisan::call('config:clear');

        $this->notify('success', __('filament-shield::filament-shield.update'));
    }

    public function generate(): void
    {
        $this->backupCurrentConfig();

        $this->generateNewConfig();

        Artisan::call('config:clear');

        Artisan::call('shield:generate');

        $this->notify('success', __('filament-shield::filament-shield.generate'));
    }

    protected function getFormActions(): array
    {
        return [
            ButtonAction::make('save')
                ->label(__('filament-shield::filament-shield.page.save'))
                ->submit(),
            ButtonAction::make('generate')
                ->label(__('filament-shield::filament-shield.page.generate'))
                ->action('generate'),
        ];
    }
}
