@slots(['indicator'])

<slider>
    <div slot-scope="{ navigate, showLeft, showRight, currentSlide, slidesTotal }">
        <div class="relative">
            <div ref="slider" {{ $attributes->twMerge('*:w-1/2 *:md:w-1/3 *:xl:w-1/4 *:px-5 *:shrink-0 *:snap-start -mx-5 flex snap-x snap-mandatory overflow-x-auto scrollbar-hide') }}>
                {{ $slot }}
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
        {{ $indicator }}
    </div>
</slider>