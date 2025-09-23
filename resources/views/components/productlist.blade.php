@props(['value', 'title' => false, 'field' => 'sku'])
@slots(['items'])

{{--
Examples:
<x-rapidez::productlist :value="['MS04', 'MS05', 'MS09']"/>
<x-rapidez::productlist value="productIds" field="entity_id"/>
<x-rapidez::productlist :value="false" filter-query-string="sku:MS04,MS05,MS09"/>
<x-rapidez::productlist :value="false" v-bind:base-filters="() => [{dslQuery}}]"/>
--}}

@if ($value !== [])
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
                    @slotdefault('before')
                        @if ($value && $value !== [])
                            <ais-configure :filters="'{{ $field }}:({{ is_array($value)
                                ? implode(' OR ', $value)
                                : "'+".$value.".join(' OR ')+'"
                            }})'"/>
                        @endif
                    @endslotdefault

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

                    {{ $after ?? '' }}
                </ais-instant-search>
            </div>
        </listing>
    </lazy>
@endif
