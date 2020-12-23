<div>
    @if(!isset($label) || (isset($label) && $label))
        <label class="font-semibold text-gray-700 text-sm" for="{{ $name }}">@lang($label ?? ucfirst($name))</label>
    @endif
    <input
        {{ $attributes->merge(['class' => 'form-input w-full', 'type' => 'text', 'placeholder' => __($label ?? ucfirst($name))]) }}
        id="{{ $name }}"
        dusk="{{ $name }}"
    >
</div>
