@slots(['content', 'label', 'icon'])
@props(['canToggleShowMore' => false])

<x-rapidez::accordion open class="border-t" :$attributes :$icon>
    <x-slot:label>
        @slotdefault('label')
            @{{ filter?.name?.replace('_', ' ') }}
        @endslotdefault
    </x-slot:label>

    <x-slot:content>
        {{ $content }}

        @if ($canToggleShowMore)
            <button
                v-if="canToggleShowMore"
                v-on:click="toggleShowMore"
                class="text-sm text-primary font-medium mt-3 hover:underline"
            >
                <template v-if="isShowingMore">
                    @lang('Less options')
                </template>
                <template v-else>
                    @lang('More options')
                </template>
            </button>
        @endif
    </x-slot:content>
</x-rapidez::accordion>