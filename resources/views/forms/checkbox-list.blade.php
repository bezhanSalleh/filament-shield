@php
    use Filament\Support\Enums\GridDirection;
    use Filament\Support\Facades\FilamentView;

    $fieldWrapperView = $getFieldWrapperView();
    $extraInputAttributeBag = $getExtraInputAttributeBag();
    $isHtmlAllowed = $isHtmlAllowed();
    $gridDirection = $getGridDirection() ?? GridDirection::Column;
    $isBulkToggleable = $isBulkToggleable();
    $isDisabled = $isDisabled();
    $isSearchable = $isSearchable();
    $statePath = $getStatePath();
    $options = $getOptions();
    $livewireKey = $getLivewireKey();
    $wireModelAttribute = $applyStateBindingModifiers('wire:model');
@endphp

<x-dynamic-component :component="$fieldWrapperView" :field="$field">
    <div
        x-data="{
            livewireId: @js($this->getId()),

            areAllCheckboxesChecked: false,

            checkboxListOptions: [],

            search: '',

            visibleCheckboxListOptions: [],

            init() {
                this.checkboxListOptions = Array.from(
                    this.$root.querySelectorAll('.fi-fo-checkbox-list-option'),
                )

                this.updateVisibleCheckboxListOptions()

                this.$nextTick(() => {
                    this.checkIfAllCheckboxesAreChecked()
                })

                Livewire.hook(
                    'commit',
                    ({ component, commit, succeed, fail, respond }) => {
                        succeed(({ snapshot, effect }) => {
                            this.$nextTick(() => {
                                if (component.id !== this.livewireId) {
                                    return
                                }

                                this.checkboxListOptions = Array.from(
                                    this.$root.querySelectorAll(
                                        '.fi-fo-checkbox-list-option',
                                    ),
                                )

                                this.updateVisibleCheckboxListOptions()

                                this.checkIfAllCheckboxesAreChecked()
                            })
                        })
                    },
                )

                this.$watch('search', () => {
                    this.updateVisibleCheckboxListOptions()
                    this.checkIfAllCheckboxesAreChecked()
                })
            },

            checkIfAllCheckboxesAreChecked() {
                this.areAllCheckboxesChecked =
                    this.visibleCheckboxListOptions.length ===
                    this.visibleCheckboxListOptions.filter((checkboxLabel) =>
                        checkboxLabel.querySelector('input[type=checkbox]:checked'),
                    ).length
            },

            toggleAllCheckboxes() {
                this.checkIfAllCheckboxesAreChecked()

                const inverseAreAllCheckboxesChecked = !this.areAllCheckboxesChecked

                this.visibleCheckboxListOptions.forEach((checkboxLabel) => {
                    const checkbox = checkboxLabel.querySelector(
                        'input[type=checkbox]',
                    )

                    if (checkbox.disabled) {
                        return
                    }

                    checkbox.checked = inverseAreAllCheckboxesChecked
                    checkbox.dispatchEvent(new Event('change'))
                })

                this.areAllCheckboxesChecked = inverseAreAllCheckboxesChecked
            },

            updateVisibleCheckboxListOptions() {
                this.visibleCheckboxListOptions = this.checkboxListOptions.filter(
                    (checkboxListItem) => {
                        if (['', null, undefined].includes(this.search)) {
                            return true
                        }

                        if (
                            checkboxListItem
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(this.search.toLowerCase())
                        ) {
                            return true
                        }

                        return checkboxListItem
                            .querySelector(
                                '.fi-fo-checkbox-list-option-description',
                            )
                            ?.innerText.toLowerCase()
                            .includes(this.search.toLowerCase())
                    },
                )
            },
        }"

        {{ $getExtraAlpineAttributeBag()->class(['fi-fo-checkbox-list']) }}
    >
        @if (! $isDisabled)
            @if ($isSearchable)
                <x-filament::input.wrapper
                    inline-prefix
                    :prefix-icon="\Filament\Support\Icons\Heroicon::MagnifyingGlass"
                    prefix-icon-alias="forms:components.checkbox-list.search-field"
                    class="fi-fo-checkbox-list-search-input-wrp"
                >
                    <input
                        placeholder="{{ $getSearchPrompt() }}"
                        type="search"
                        x-model.debounce.{{ $getSearchDebounce() }}="search"
                        class="fi-input fi-input-has-inline-prefix"
                    />
                </x-filament::input.wrapper>
            @endif

            @if ($isBulkToggleable && count($options))
                <div
                    x-cloak
                    class="fi-fo-checkbox-list-actions"
                    wire:key="{{ $livewireKey }}.actions"
                >
                    <span
                        x-show="! areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.select-all"
                    >
                        {{ $getAction('selectAll') }}
                    </span>

                    <span
                        x-show="areAllCheckboxesChecked"
                        x-on:click="toggleAllCheckboxes()"
                        wire:key="{{ $livewireKey }}.actions.deselect-all"
                    >
                        {{ $getAction('deselectAll') }}
                    </span>
                </div>
            @endif
        @endif

        <div
            {{
                $getExtraAttributeBag()
                    ->grid($getColumns(), $gridDirection)
                    ->merge([
                        'x-show' => $isSearchable ? 'visibleCheckboxListOptions.length' : null,
                    ], escape: false)
                    ->class([
                        'fi-fo-checkbox-list-options',
                    ])
            }}
        >
            @forelse ($options as $value => $label)
                <div
                    wire:key="{{ $livewireKey }}.options.{{ $value }}"
                    @if ($isSearchable)
                        x-show="
                            $el
                                .querySelector('.fi-fo-checkbox-list-option-label')
                                ?.innerText.toLowerCase()
                                .includes(search.toLowerCase()) ||
                                $el
                                    .querySelector('.fi-fo-checkbox-list-option-description')
                                    ?.innerText.toLowerCase()
                                    .includes(search.toLowerCase())
                        "
                    @endif
                    class="fi-fo-checkbox-list-option-ctn"
                >
                    <label class="fi-fo-checkbox-list-option">
                        <input
                            type="checkbox"
                            {{
                                $extraInputAttributeBag
                                    ->merge([
                                        'disabled' => $isDisabled || $isOptionDisabled($value, $label),
                                        'value' => $value,
                                        'wire:loading.attr' => 'disabled',
                                        $wireModelAttribute => $statePath,
                                        'x-on:change' => $isBulkToggleable ? 'checkIfAllCheckboxesAreChecked()' : null,
                                    ], escape: false)
                                    ->class([
                                        'fi-checkbox-input',
                                        'fi-valid' => ! $errors->has($statePath),
                                        'fi-invalid' => $errors->has($statePath),
                                    ])
                            }}
                        />

                        <div class="fi-fo-checkbox-list-option-text">
                            <span class="fi-fo-checkbox-list-option-label">
                                @if ($isHtmlAllowed)
                                    {!! $label !!}
                                @else
                                    {{ $label }}
                                @endif
                            </span>

                            @if ($hasDescription($value))
                                <p
                                    class="fi-fo-checkbox-list-option-description"
                                >
                                    {{ $getDescription($value) }}
                                </p>
                            @endif
                        </div>
                    </label>
                </div>
            @empty
                <div wire:key="{{ $livewireKey }}.empty"></div>
            @endforelse
        </div>

        @if ($isSearchable)
            <div
                x-cloak
                x-show="search && ! visibleCheckboxListOptions.length"
                class="fi-fo-checkbox-list-no-search-results-message"
            >
                {{ $getNoSearchResultsMessage() }}
            </div>
        @endif
    </div>
</x-dynamic-component>
