<global-slideover-instance>
    <template v-slot="{ title, position }" v-cloak>
        <x-rapidez::slideover
            id="slideover-global"
            position=""
            closedby="any"
            v-bind:class="position === 'right'
                ? 'ml-auto translate-x-full starting:open:translate-x-full open:translate-x-0'
                : '-translate-x-full starting:open:-translate-x-full open:translate-x-0'"
        >
            <x-rapidez::slideover.header>
                <div class="max-w-full px-10 truncate font-semibold" v-html="title"></div>
                <x-rapidez::slideover.close commandfor="slideover-global" command="close" :aria-label="__('Close')" />
            </x-rapidez::slideover.header>
            <x-rapidez::slideover.content class="max-h-none">
                <div id="global-slideover-content"></div>
            </x-rapidez::slideover.content>
        </x-rapidez::slideover>
    </template>
</global-slideover-instance>
