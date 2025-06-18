@php
    use Filament\Support\View\Components\ToggleComponent;
    use Illuminate\Support\Arr;
@endphp

@props([
    'state',
    'offColor' => 'gray',
    'offIcon' => null,
    'onColor' => 'primary',
    'onIcon' => null,
])

<button
    x-data="{
        state: {{ $state }},
        init() {
            this.syncStateFromCheckboxes()

            this.checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => this.syncStateFromCheckboxes())
            })
        },

        get checkboxes() {
            return Array.from(document.querySelectorAll('.fi-checkbox-input'))
        },

        get checkboxLists() {
            return Array.from(document.querySelectorAll('.fi-fo-checkbox-list'))
        },

        toggle() {
            this.state = !this.state

            this.checkboxes.forEach(checkbox => {
                checkbox.checked = this.state
                {{-- checkbox.dispatchEvent(new Event('change', { bubbles: true })) --}}
            })
        },

        syncStateFromCheckboxes() {
            this.state = this.checkboxes.every(checkbox => checkbox.checked)
        },
    }"

    x-on:click="toggle"
    x-bind:aria-checked="state?.toString()"
    x-bind:class="
        state ? @js(Arr::toCssClasses([
                    'fi-toggle-on',
                    ...\Filament\Support\get_component_color_classes(ToggleComponent::class, $onColor),
                ])) : @js(Arr::toCssClasses([
                            'fi-toggle-off',
                            ...\Filament\Support\get_component_color_classes(ToggleComponent::class, $offColor),
                        ]))
    "
    @if ($state)
        x-cloak
    @endif
    {{
        $attributes
            ->merge([
                'role' => 'switch',
                'type' => 'button',
            ], escape: false)
            ->class(['fi-toggle'])
    }}
>
    <div>
        <div aria-hidden="true">
            {{ \Filament\Support\generate_icon_html($offIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
        </div>

        <div aria-hidden="true">
            {{ \Filament\Support\generate_icon_html($onIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
        </div>
    </div>
</button>

@if ($state)
    <div
        x-cloak="inline-flex"
        wire:ignore
        @class([
            'fi-toggle fi-toggle-on fi-hidden',
            ...\Filament\Support\get_component_color_classes(ToggleComponent::class, $onColor),
        ])
    >
        <div>
            <div aria-hidden="true"></div>

            <div aria-hidden="true">
                {{ \Filament\Support\generate_icon_html($onIcon, size: \Filament\Support\Enums\IconSize::ExtraSmall) }}
            </div>
        </div>
    </div>
@endif
