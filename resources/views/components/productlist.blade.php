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
                            <x-rapidez::slider>
                                <template v-for="(item, count) in items">
                                    @include('rapidez::listing.partials.item', ['slider' => true])
                                </template>
                                <x-slot:indicator>
                                    <div v-show="slidesTotal > 1" class="flex flex-row justify-center w-full mt-9">
                                        <div
                                            v-for="slide, index in slidesTotal"
                                            v-on:click="navigate(index)"
                                            class="relative bg rounded-full border size-4 mx-1 cursor-pointer"
                                            :class="{ 'bg-active border-active': index === currentSlide }">
                                        </div>
                                    </div>
                                </x-slot:indicator>
                            </x-rapidez::slider>
                        </div>
                    </ais-hits>
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
@endif
