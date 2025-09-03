@props(['value', 'title' => false, 'field' => 'sku'])
@slots(['items'])

@if ($value)
    <lazy v-slot="{ intersected }">
        <listing
            {{ $attributes }}
            v-if="intersected"
            v-slot="listingSlotProps"
            v-cloak
        >
            <div>
                <ais-instant-search
                    v-if="listingSlotProps.searchClient"
                    :search-client="listingSlotProps.searchClient"
                    :index-name="listingSlotProps.index"
                    :middlewares="listingSlotProps.middlewares"
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
                            @slotdefault('items')
                                <x-rapidez::slider />
                            @endslotdefault
                        </div>
                    </ais-hits>
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
@endif
