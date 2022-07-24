<?php

namespace BezhanSalleh\FilamentShield\Resources\RoleResource\Pages;

use Filament\Forms;
use Filament\Pages\Actions;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Pages\Page;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Filament\Pages\Contracts\HasFormActions;
use BezhanSalleh\FilamentShield\Models\Setting;
use BezhanSalleh\FilamentShield\Resources\RoleResource;
use Filament\Resources\Pages\Concerns\UsesResourceForm;

class ViewShieldSettings extends Page implements HasFormActions
{
    use UsesResourceForm;

    protected static string $resource = RoleResource::class;

    protected static string $view = 'filament-shield::pages.shield-settings';

    protected function getTitle(): string
    {
        return __('filament-shield::filament-shield.page.name');
    }

    public function mount(): void
    {
        abort_unless((static::getResource()::canViewAny() && config('filament-shield.settings.gui_enabled')) || Filament::auth()->user()->hasRole(config('filament-shield.super_admin.name')), 403);

        $this->form->fill(
            config('filament-shield.settings.driver') === 'database'
            ? Setting::pluck('value', 'key')->toArray()
            : config('filament-shield')
        );

    }

    protected function getFormSchema(): array
    {
        $layout = Forms\Components\Card::class;

        return [
            Forms\Components\Grid::make()
                ->schema([
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('super_admin.enabled')
                                ->label(__('filament-shield::filament-shield.labels.super_admin.toggle_input'))
                                ->hint(fn ($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('super_admin.name')
                                ->label(__('filament-shield::filament-shield.labels.super_admin.text_input'))
                                ->visible(fn ($get) => $get('super_admin.enabled'))
                                ->required(fn ($get) => $get('super_admin.enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),

                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('filament_user.enabled')
                                ->label(__('filament-shield::filament-shield.labels.filament_user.toggle_input'))
                                ->hint(fn ($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->required()
                                ->reactive(),
                            Forms\Components\TextInput::make('filament_user.name')
                                ->label(__('filament-shield::filament-shield.labels.filament_user.text_input'))
                                ->visible(fn ($get) => $get('filament_user.enabled'))
                                ->required(fn ($get) => $get('filament_user.enabled')),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('register_role_policy.enabled')
                                ->label(__('filament-shield::filament-shield.labels.role_policy.toggle_input'))
                                ->hint(fn ($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.yes").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.no").'</span>')
                                ->default(true)
                                ->helperText('<span class="text-md text-gray-600 leading-loose">'.__("filament-shield::filament-shield.labels.role_policy.message").'</span>')
                                ->reactive()
                                ->required(),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\TextInput::make('auth_provider_model.fqcn')
                                ->label(__('filament-shield::filament-shield.settings.auth_provider.label'))
                                ->helperText(__('filament-shield::filament-shield.settings.auth_provider.helper_text'))
                                ->default(config('filament-shield.auth_provider_model.fqcn'))
                                ->required(),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Select::make('settings.driver')
                                ->options([
                                    'file' => __('filament-shield::filament-shield.settings.driver.file'),
                                    'database' => __('filament-shield::filament-shield.settings.driver.database'),
                                ])
                                ->label(__('filament-shield::filament-shield.settings.driver.label'))
                                ->helperText(__('filament-shield::filament-shield.settings.driver.helper_text'))
                                ->default(config('filament-shield.settings.driver'))
                                ->disablePlaceholderSelection()
                                ->required(),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Toggle::make('settings.gui_enabled')
                                ->label(__('filament-shield::filament-shield.settings.gui.label'))
                                ->hint(fn ($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.yes").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.no").'</span>')
                                ->default(config('filament-shield.settings.gui_enabled'))
                                ->helperText('<span class="text-md text-gray-600 leading-loose">'.__("filament-shield::filament-shield.settings.gui.helper_text").'</span>')
                                ->required(),
                        ])
                        ->columns(1)
                        ->columnSpan(1),
                    $layout::make()
                        ->schema([
                            Forms\Components\Grid::make()
                                ->schema([
                                    Forms\Components\Toggle::make('shield_resource.enabled')
                                        ->label(__('filament-shield::filament-shield.settings.resource.name'))
                                        ->hint(fn ($state) => $state ? '<span class="font-bold text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : '<span class="font-bold text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                        ->required()
                                        ->reactive(),
                                    Forms\Components\TextInput::make('shield_resource.resource')
                                        ->label(__('filament-shield::filament-shield.settings.resource.label'))
                                        ->visible(fn ($get) => $get('shield_resource.enabled'))
                                        ->required(fn ($get) => $get('shield_resource.enabled')),
                                ])
                                ->columns(1)
                                ->columnSpan('full'),
                            Forms\Components\Grid::make()
                                ->visible(fn ($get) => $get('shield_resource.enabled'))
                                ->extraAttributes(['class' => 'border-0 shadow-sm','style' => 'border:1px solid #d1d5db8c!important'])
                                ->schema([
                                    Forms\Components\TextInput::make('shield_resource.slug')
                                        ->label(__('filament-shield::filament-shield.settings.resource.slug'))
                                        ->required(fn ($get) => $get('shield_resource.enabled')),
                                ])
                                ->columns(1)
                                ->columnSpan(1),
                            Forms\Components\Grid::make()
                                ->visible(fn ($get) => $get('shield_resource.enabled'))
                                ->extraAttributes(['class' => 'border-0 shadow-sm','style' => 'border:1px solid #d1d5db8c!important'])
                                ->schema([
                                    Forms\Components\TextInput::make('shield_resource.navigation_sort')
                                        ->label(__('filament-shield::filament-shield.settings.resource.navigation_sort'))
                                        ->required(fn ($get) => $get('shield_resource.enabled')),
                                ])
                                ->columns(1)
                                ->columnSpan(1),
                        ])
                        ->columns([
                            'sm' => 1,
                            'lg' => 2,
                        ])
                ])
                ->columns([
                    'sm' => 1,
                    'md' => 2,
                    'lg' => 3,
                ]),
            $layout::make()
            ->schema([
                    Forms\Components\Placeholder::make('')
                        ->label(__('filament-shield::filament-shield.labels.permission_prefixes.placeholder')),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\TagsInput::make('permission_prefixes.resource')
                                ->label(__('filament-shield::filament-shield.labels.permission_prefixes.resource'))
                                ->placeholder(__('filament-shield::filament-shield.labels.permission_prefixes.resource.placeholder'))
                                ->required()
                                ->separator(','),
                            Forms\Components\TextInput::make('permission_prefixes.page')
                                ->label(__('filament-shield::filament-shield.labels.permission_prefixes.page'))
                                ->required(),
                            Forms\Components\TextInput::make('permission_prefixes.widget')
                                ->label(__('filament-shield::filament-shield.labels.permission_prefixes.widget'))
                                ->required(),
                        ])
                        ->columns(3),
                ]),

            $layout::make()
                ->schema([
                    Forms\Components\Placeholder::make('')
                        ->label(__('filament-shield::filament-shield.labels.entities.placeholder')),
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Toggle::make('entities.resources')
                                ->label(__('filament-shield::filament-shield.labels.entities.resources'))
                                ->helperText(fn ($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities.pages')
                                ->label(__('filament-shield::filament-shield.labels.entities.pages'))
                                ->helperText(fn ($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities.widgets')
                                ->label(__('filament-shield::filament-shield.labels.entities.widgets'))
                                ->helperText(fn ($state) => $state ? __("filament-shield::filament-shield.labels.entities.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                            Forms\Components\Toggle::make('entities.custom_permissions')
                                ->label(__('filament-shield::filament-shield.labels.entities.custom_permissions'))
                                ->helperText(fn ($state) => $state ? __("filament-shield::filament-shield.labels.entities.custom_permissions.message").' <span class="font-medium text-success-700">'.__("filament-shield::filament-shield.labels.status.enabled").'</span>' : __("filament-shield::filament-shield.labels.entities.custom_permissions.message").'<span class="font-medium text-primary-700">'.__("filament-shield::filament-shield.labels.status.disabled").'</span>')
                                ->reactive(),
                        ])
                        ->columns(4),
                ]),
            $layout::make()
                ->visible(fn ($get): bool => (bool) $get('entities.resources'))
                ->schema([
                    Forms\Components\Grid::make()
                    ->schema([
                            Forms\Components\Placeholder::make('')
                                ->content(new HtmlString('<span class="font-medium text-sm text-gray-700 dark:text-gray-300">Generator Option</span>')),
                            Forms\Components\Radio::make('generator.option')
                                ->label('')
                                ->options([
                                    'policies_and_permissions' => __("filament-shield::filament-shield.settings.generator_options.policies_and_permissions"),
                                    'policies' => __("filament-shield::filament-shield.settings.generator_options.policies"),
                                    'permissions' => __("filament-shield::filament-shield.settings.generator_options.permissions"),
                                ])
                                ->inline(),
                        ])
                        ->columns(1),
                ]),

            $layout::make()
                ->schema([
                    Forms\Components\Grid::make()
                        ->schema([
                            Forms\Components\Placeholder::make('')
                                ->label(__('filament-shield::filament-shield.labels.exclude.placeholder'))
                                ->content(__('filament-shield::filament-shield.labels.exclude.message'))
                                ->extraAttributes(['class' => 'text-sm text-gray-500']),
                            Forms\Components\Toggle::make('exclude.enabled')
                                ->label(fn ($state): string => $state ? __("filament-shield::filament-shield.labels.status.enabled") : __("filament-shield::filament-shield.labels.status.disabled"))
                                ->reactive(),
                            Forms\Components\Grid::make()
                                ->visible(fn ($get) => $get('exclude.enabled'))
                                ->schema([
                                    Forms\Components\Select::make('exclude.resources')
                                        ->multiple()
                                        ->label(__("filament-shield::filament-shield.labels.exclude.resources"))
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.resources.placeholder"))
                                        ->options(
                                            collect(Filament::getResources())
                                                ->reduce(function ($resources, $resource) {
                                                    $resources[Str::afterLast($resource, '\\')] = Str::afterLast($resource, '\\');

                                                    return $resources;
                                                }, collect())->toArray()
                                        )
                                        ->preload()
                                        ,
                                    Forms\Components\Select::make("exclude.pages")
                                        ->multiple()
                                        ->label(__("filament-shield::filament-shield.labels.exclude.pages"))
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.pages.placeholder"))
                                        ->options(collect(Filament::getPages())
                                            ->reduce(function ($pages, $page) {
                                                $name = Str::of($page)
                                                    ->after('Pages\\')
                                                    ->replace('\\', '');

                                                $pages["{$name}"] = "{$name}";

                                                return $pages;
                                            }, collect())->toArray())
                                        ->preload(),
                                    Forms\Components\Select::make('exclude.widgets')
                                        ->multiple()
                                        ->label(__("filament-shield::filament-shield.labels.exclude.widgets"))
                                        ->placeholder(__("filament-shield::filament-shield.labels.exclude.widgets.placeholder"))
                                        ->options(
                                            collect(Filament::getWidgets())
                                            ->reduce(function ($widgets, $widget) {
                                                $name = Str::of($widget)
                                                        ->after('Widgets\\')
                                                        ->replace('\\', '');
                                                $widgets["{$name}"] = "{$name}";

                                                return $widgets;
                                            }, collect())->toArray()
                                        )
                                        ->preload(),

                                ])
                                ->columns(3),

                        ]),
                ]),
        ];
    }

    public function save(bool $notify = true): void
    {
        $data = $this->form->getState();

        $data['permission_prefixes']['resource'] = explode(',', $data['permission_prefixes']['resource']);

        dump('config before change', config('filament-shield'));
        config(['filament-shield' => null ]);
        dump('config after null', config('filament-shield'));

        static::updateConfig($data);

        dump('config after change', config('filament-shield'));

        if ($notify) {
            $this->notify('primary',"resource class ".config('filament-shield.shield_resource.resource'));
            $this->notify('success', __('filament-shield::filament-shield.update'));
        }
    }

    protected function getFormActions(): array
    {
        return [

            Actions\Action::make('save')
                ->label(__('filament-shield::filament-shield.page.save'))
                ->submit()
                ->color('success'),

            Actions\Action::make('generate')
                ->label(__('filament-shield::filament-shield.page.generate'))
                ->action(function () {
                    $this->save(false);

                    Artisan::call('shield:generate');

                    $this->notify('success', __('filament-shield::filament-shield.generate'));
                })
                ->color('primary')
                ->requiresConfirmation(),

            Actions\Action::make('load_defaults')
                ->label(__('filament-shield::filament-shield.page.load_default_settings'))
                ->action(function () {
                    $this->form->fill(Setting::pluck('default', 'key')->toArray());

                    $this->save(false);

                    $this->notify('success', __('filament-shield::filament-shield.loaded_default_settings'));
                })
                ->requiresConfirmation()
                ->color('warning')
                ->visible(config('filament-shield.settings.driver') === 'database'),

            Actions\Action::make('cancel')
                ->url(static::$resource::getUrl(name: 'index'))
                ->label(__('filament-shield::filament-shield.page.cancel'))
                ->color('secondary'),

        ];
    }

    protected static function updateConfig(array $data): void
    {
        if (config('filament-shield.settings.driver') === 'database') {
            foreach ($data as $key => $value) {
                Setting::updateOrCreate([
                    'key' => $key,
                ], [
                   'value' => $value,
                ]);
            }

            config()->set('filament-shield', Setting::pluck('value', 'key')->toArray());
        } else {
            $eol = PHP_EOL;
            File::put(config_path('filament-shield.php'),$data);
            File::prepend(config_path('filament-shield.php'),"<?php{$eol}return{$eol}");
        }
    }
}
