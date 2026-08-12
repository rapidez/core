<x-rapidez::input {{ $attributes->merge([
    'v-bind:disabled' => 'loading.value',
]) }} />
