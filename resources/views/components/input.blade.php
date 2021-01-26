<input
    {{ $attributes->merge(['class' => 'w-full py-2 px-3 border-gray-300 rounded focus:ring-green-500 focus:border-green-500', 'type' => 'text', 'placeholder' => __($placeholder ?? ucfirst($name))]) }}
    id="{{ $name }}"
    dusk="{{ $name }}"
>
