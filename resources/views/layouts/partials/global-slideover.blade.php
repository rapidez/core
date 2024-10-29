<template v-if="globalSlideover.content ?? false" v-cloak>
    <x-rapidez::slideover
        id="slideover-global"
        v-bind:class="{ '-right-full peer-checked:right-0 !left-auto': (globalSlideover.position ?? 'left') === 'right' }"
    >
        <x-slot:title>
            <div v-html="globalSlideover.title ?? ''"></div>
        </x-slot:title>

        <div v-html="globalSlideover.content ?? ''"></div>
    </x-rapidez::slideover>
</template>
