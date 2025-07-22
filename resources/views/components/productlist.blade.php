@props(['value', 'title' => false, 'field' => 'sku'])

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
                            <x-rapidez::slider />
                        </div>
                    </ais-hits>
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
@endif
