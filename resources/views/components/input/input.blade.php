<x-blade-components::input {{ $attributes->merge([
    'v-bind:disabled' => 'loading.value',
]) }} />
