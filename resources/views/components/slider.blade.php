@props(['reference' => uniqid('slider')])
@slots(['items', 'indicator'])

<slider reference="{{ $reference }}">
    <div slot-scope="{ navigate, showLeft, showRight, currentSlide, slidesTotal }">
        <div {{ $attributes->twMerge('relative') }}>
            <div ref="{{ $reference }}" {{ $items->attributes->twMerge('*:w-1/2 *:md:w-1/3 *:xl:w-1/4 *:px-5 *:shrink-0 *:snap-start -mx-5 flex snap-x snap-mandatory overflow-x-auto scrollbar-hide') }}>
                @slotdefault('items')
                    <template v-for="(item, count) in items">
                        @include('rapidez::listing.partials.item', ['slider' => true])
                    </template>
                @endslotdefault
            </div>
            <x-rapidez::button.slider
                class="absolute left-0 top-1/2 sm:-translate-x-1/2 -translate-y-1/2"
                v-if="showLeft"
                v-on:click="navigate(currentSlide - 1)"
                :aria-label="__('Prev')"
            >
                <x-heroicon-o-chevron-left class="size-6 shrink-0"/>
            </x-rapidez::button.slider>
            <x-rapidez::button.slider
                class="absolute right-0 top-1/2 sm:translate-x-1/2 -translate-y-1/2"
                v-if="showRight"
                v-on:click="navigate(currentSlide + 1)"
                :aria-label="__('Next')"
            >
                <x-heroicon-o-chevron-right class="size-6 shrink-0"/>
            </x-rapidez::button.slider>
        </div>
        @slotdefault('indicator')
            <div v-if="slidesTotal > 1" class="flex flex-row justify-center w-full mt-9">
                <div
                    v-for="index in slidesTotal"
                    v-on:click="navigate(index)"
                    class="relative bg rounded-full border size-4 mx-1 cursor-pointer"
                    :class="{ 'bg-active border-active': index === currentSlide }">
                </div>
            </div>
        @endslotdefault
    </div>
</slider>
