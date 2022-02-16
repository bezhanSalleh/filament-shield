<?php

namespace App\Filament\Resources\Shield\RoleResource\Pages;

use Closure;
use Filament\Forms;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Filament\Pages\Actions\ButtonAction;
use App\Filament\Resources\Shield\RoleResource;
use BezhanSalleh\FilamentShield\Commands\Concerns;
use Illuminate\Support\Facades\Artisan;

class Settings extends Page
{
    use Concerns\CanManipulateFiles;

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament-shield::pages.settings';

    public function mount(): void
    {
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
                                ->label('Super Admin Role')
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">Enabled</span>' : '<span class="font-bold text-primary-700">Disabled</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('super_admin_role_name')
                                ->label('')
                                ->prefix('Role Name')
                                ->afterStateHydrated(fn(Closure $set, Closure $get, $state) => $set($state,Str::of($get($state))->snake()))
                                ->visible(fn($get) => $get('super_admin_role_enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),

                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('filament_user_role_enabled')
                                ->label('Filament User Role')
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">Enabled</span>' : '<span class="font-bold text-primary-700">Disabled</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('filament_user_role_name')
                                ->label('')
                                ->prefix('Role Name')
                                ->visible(fn($get) => $get('filament_user_role_enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('register_role_policy')
                                ->label('Role Policy Registered?')
                                ->hint(fn($state) => $state ? '<span class="font-bold text-success-700">Yes</span>' : '<span class="font-bold text-primary-700">No</span>')
                                ->default(true)
                                ->helperText('<span class="text-md text-gray-600 leading-loose">Ensure the policy is registered and the permissions are enforced</span>')
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
                        ->label('Default Permission Prefixes'),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TagsInput::make('resource_permission_prefixes')
                                ->label('Resource')
                                ->placeholder('Add Custom Resource Permission')
                                ->required()
                                ->separator(','),
                            Forms\Components\TextInput::make('page_permission_prefix')
                                ->label('Page')
                                ->required(),
                            Forms\Components\TextInput::make('widget_permission_prefix')
                                ->label('Widget')
                                ->required(),
                        ])
                        ->columns(3)
                ]),
            $layout::make()
                ->schema([
                    Forms\Components\Placeholder::make('Entities\'s Permission Generator Options')
                        ->label('Entity Permission Generators & Tabs'),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Toggle::make('entities_resources')
                                ->label('Resources')
                                ->default(config('filament-shield.entities.resources'))
                                ->helperText(fn($state) => $state ? 'Generator & Tab is <span class="font-medium text-success-700">Enabled</span>' : 'Generator & Tab is <span class="font-medium text-primary-700">Disabled</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_pages')
                                ->label('Pages')
                                ->helperText(fn($state) => $state ? 'Generator & Tab is: <span class="font-medium text-success-700">Enabled</span>' : 'Generator & Tab is: <span class="font-medium text-primary-700">Disabled</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_widgets')
                                ->label('Widgets')
                                ->helperText(fn($state) => $state ? 'Generator & Tab is <span class="font-medium text-success-700">Enabled</span>' : 'Generator & Tab is <span class="font-medium text-primary-700">Disabled</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities_custom_permissions')
                                ->label('Custom Permissions')
                                ->helperText(fn($state) => $state ? 'Tab is <span class="font-medium text-success-700">Enabled</span>' : 'Tab is <span class="font-medium text-primary-700">Disabled</span>')
                                ->reactive(),
                        ])
                        ->columns(4),
                ]),

            $layout::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Placeholder::make('Exclusion Mode')
                                ->label('Exclusion Mode')
                                ->content('By Enabling the Exclusion Mode you can instruct permission generator to skip creating permissions for the entities you select.')
                                ->extraAttributes(['class' => 'text-sm text-gray-500']),
                            Forms\Components\Toggle::make('exclude_enabled')
                                ->label(fn($state): string => $state ? 'Enabled' : 'Disabled')
                                ->default(config('filament-shield.exclude.enabled'))
                                ->reactive(),
                            Forms\Components\Grid::make()
                                ->visible(fn($get) => $get('exclude_enabled'))
                                ->schema([
                                    Forms\Components\MultiSelect::make('exclude_resources')
                                        ->placeholder('Select resources ...')
                                        ->options(function() {
                                            return collect(Filament::getResources())
                                                ->reduce(function ($resources, $resource) {
                                                    $resources[Str::afterLast($resource, '\\')] = Str::afterLast($resource, '\\');
                                                    return $resources;
                                                }, []);
                                        })
                                        ->label('Resources'),
                                    Forms\Components\MultiSelect::make('exclude_pages')
                                        ->placeholder('Select pages ...')
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
                                        ->label('Pages'),
                                    Forms\Components\MultiSelect::make('exclude_widgets')
                                        ->placeholder('Select widgets ...')
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
                                        ->label('Widgets'),
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
                'exclude_enabled' => $this->exclude_enabled ? 'true' : 'false',
                'exclude_pages' => json_encode($this->exclude_pages),
                'exclude_widgets' => json_encode($this->exclude_widgets),
                'exclude_resources' => json_encode($this->exclude_resources),
            ]
        );
    }

    public function save(): void
    {
        $this->generateNewConfig();

        Artisan::call('config:clear');

        $this->notify('success', __('filament-shield::filament-shield.update'));
    }

    public function generate(): void
    {
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
