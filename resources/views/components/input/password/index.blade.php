<div>
    <toggler>
        <div class="relative" slot-scope="{ isOpen, toggle }">
            <x-rapidez::input
                class="[&>input]:pr-12"
                type="password"
                v-bind:type="isOpen ? 'text' : 'password'"
                {{ $attributes }}
            />
            @if (!$attributes['disabled'] ?? false)
                <div v-on:click="toggle" class="absolute right-5 top-1/2 -translate-y-1/2 cursor-pointer">
                    <x-heroicon-o-eye v-if="isOpen" class="h-5" v-cloak/>
                    <x-heroicon-o-eye-slash v-else class="h-5"/>
                </div>
            @endif
        </div>
    </toggler>
</div>