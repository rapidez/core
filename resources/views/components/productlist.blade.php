@props(['value', 'title' => false, 'field' => 'sku.keyword'])

@if ($value)
    <lazy v-slot="{ intersected }">
        <listing
            {{ $attributes }}
            v-if="intersected"
            v-slot="{ loaded, index, searchClient, middlewares }"
            v-cloak
        >
            <div>
                <ais-instant-search
                    v-if="searchClient"
                    :search-client="searchClient"
                    :index-name="index"
                    :middlewares="middlewares"
                >
                    <ais-configure :filters="'{{ $field }}:({{ is_array($value)
                        ? implode(' OR ', $value)
                        : "'+".$value.".join(' OR ')+'"
                    }})'"/>

                    <ais-hits v-slot="{ items, sendEvent }">
                        <div v-if="items.length" class="flex flex-col gap-5">
                            @if ($title)
                                <strong class="font-bold text-2xl">
                                    @lang($title)
                                </strong>
                            @endif

                            <slider>
                                <div slot-scope="{ navigate, showLeft, showRight, currentSlide, slidesTotal }">
                                    <div class="relative">
                                        {{--
                                        TODO: @frontend, the listing has been changed
                                        to a grid, do we also need that here? As
                                        the item partials is shared.
                                        --}}
                                        <div ref="slider" class="*:w-1/2 *:md:w-1/3 *:xl:w-1/4 *:px-5 *:shrink-0 *:snap-start -mx-5 flex snap-x snap-mandatory overflow-x-auto scrollbar-hide">
                                            <template v-for="(item, count) in items">
                                                @include('rapidez::listing.partials.item', ['slider' => true])
                                            </template>
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
                                    <div v-show="slidesTotal > 1" class="flex flex-row justify-center w-full mt-[35px]">
                                        <div
                                            v-for="slide, index in slidesTotal"
                                            v-on:click="navigate(index)"
                                            class="relative bg rounded-full border size-4 mx-1 cursor-pointer"
                                            :class="{ 'bg-emphasis border-0': index === currentSlide }">
                                        </div>
                                    </div>
                                </div>
                            </slider>
                        </div>
                    </ais-hits>
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
    {{--
    TODO: Look up here, there is a lot of nesting,
    could we extract some to clean this up?
    --}}
@endif
