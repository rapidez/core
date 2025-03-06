<global-slideover-instance>
    <template v-slot="{ content, title, position }" v-cloak>
        <x-rapidez::slideover
            id="slideover-global"
            v-bind:class="{ '-right-full peer-checked:right-0 left-auto!': (position ?? 'left') === 'right' }"
        >
            <x-slot:title>
                <div v-html="title"></div>
            </x-slot:title>

            <div id="global-slideover-content"></div>
        </x-rapidez::slideover>
    </template>
</global-slideover-instance>
