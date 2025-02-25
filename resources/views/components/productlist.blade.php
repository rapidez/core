@props(['value', 'title' => false, 'field' => 'sku.keyword'])

@if ($value)
    <lazy v-slot="{ intersected }">
        <listing v-if="intersected">
            <x-rapidez::reactive-base>
                <reactive-list
                    component-id="{{ md5(serialize($value)) }}"
                    data-field="{{ $field }}"
                    :size="999"
                    :default-query="function () { return { query: { terms: { '{{ $field }}': {!!
                        is_array($value)
                            ? "['".implode("','", $value)."']"
                            : $value
                    !!} } } } }"
                >
                    @if ($title)
                        <strong class="font-bold text-2xl mt-5" slot="renderResultStats">
                            @lang($title)
                        </strong>
                    @else
                        <div slot="renderResultStats"></div>
                    @endif

                    <div slot="renderNoResults"></div>

                    <div class="mt-5" slot="render" slot-scope="{ data, loading }" v-if="!loading && data?.length">
                        <slider>
                            <div slot-scope="{ navigate, showLeft, showRight, currentSlide, slidesTotal }">
                                <div class="relative">
                                    <div ref="slider" class="sm:*:w-1/2 md:*:w-1/3 xl:*:w-1/3 *:px-0.5 sm:*:px-2 *:shrink-0 *:snap-start -mx-2 -mx-4 flex snap-x snap-mandatory overflow-x-auto scroll-smooth scrollbar-hide sm:-mx-1">
                                        <template v-for="item in data">
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
                </reactive-list>
            </x-rapidez::reactive-base>
        </listing>
    </lazy>
@endif
