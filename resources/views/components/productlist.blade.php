@props(['value', 'title' => false, 'field' => 'sku.keyword'])

@if($value)
    <reactive-base v-cloak :app="config.es_prefix + '_products_' + config.store" :url="config.es_url">
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
            @if($title)
                <strong class="font-bold text-2xl mt-5" slot="renderResultStats">
                    @lang($title)
                </strong>
            @else
                <div slot="renderResultStats"></div>
            @endif

            <div slot="renderNoResults"></div>

            <div class="relative" slot="render" slot-scope="{ data, loading }" v-if="!loading">
                <slider>
                    <div slot-scope="{ navigate, showLeft, showRight, currentSlide }">
                        <div class="flex mt-5 overflow-x-auto snap" ref="slider">
                            <template v-for="item in data">
                                @include('rapidez::listing.partials.item', ['slider' => true])
                            </template>
                        </div>
                        <x-rapidez::button variant="slider" class="absolute left-0 top-1/2 transform -translate-y-1/2" v-if="showLeft" v-on:click="navigate(currentSlide - 1)" aria-label="@lang('Prev')">
                            <x-heroicon-o-chevron-left class="w-6 h-6"/>
                        </x-rapidez::button>
                        <x-rapidez::button variant="slider" class="absolute right-0 top-1/2 transform -translate-y-1/2" v-if="showRight" v-on:click="navigate(currentSlide + 1)" aria-label="@lang('Next')">
                            <x-heroicon-o-chevron-right class="w-6 h-6"/>
                        </x-rapidez::button>
                        <div class="flex flex-row justify-center w-full mt-[35px]">
                            <div
                                v-for="item, index in data"
                                v-on:click="navigate(index)"
                                class="relative bg-white rounded-full border border-gray-300 w-[15px] h-[15px] mx-1 z-20 cursor-pointer"
                                :class="{ 'bg-primary border-0': index === currentSlide }">
                            </div>
                        </div>
                    </div>
                </slider>
            </div>
        </reactive-list>
    </reactive-base>
@endif
