@props(['value', 'title' => false, 'field' => 'sku'])
@slots(['items'])
@use(Rapidez\Core\Actions\Listing\GetInitialListingItems)

{{--
Examples:
<x-rapidez::productlist :value="['MS04', 'MS05', 'MS09']"/>
<x-rapidez::productlist value="productIds" field="entity_id"/>
<x-rapidez::productlist :value="false" filter-query-string="sku:MS04,MS05,MS09"/>
<x-rapidez::productlist :value="false" v-bind:base-filters="() => [{dslQuery}}]"/>
--}}

@if (!is_iterable($value) || count($value))
    @php
        $initialItems = is_iterable($value)
            ? app(GetInitialListingItems::class)->forValues($field, collect($value)->values()->all())
            : [];
    @endphp
    <lazy v-slot="{ intersected }">
        @if (is_string($value)) <template v-if="{{ $value }}.length"> @endif
            <listing-shell v-if="intersected" v-cloak v-bind:initial-items='@json($initialItems)' v-slot="{ items, sendEvent, setLive }">
                @include('rapidez::listing.partials.items-list')

                <div v-show="!items.length">
                    <listing
                        {{ $attributes }}
                        v-slot="listingSlotProps"
                        v-cloak
                        v-on:request-complete="setLive"
                    >
                        <div ref="root">
                            <ais-instant-search
                                v-if="listingSlotProps.searchClient"
                                :future="{ preserveSharedStateOnUnmount: true }"
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

                                <ais-hits v-slot="{ items, sendEvent }" v-bind:transform-items="listingSlotProps.transformItems">
                                    @include('rapidez::listing.partials.items-list')
                                </ais-hits>

                                {{ $after ?? '' }}
                            </ais-instant-search>
                        </div>
                    </listing>
                </div>
            </listing-shell>
        @if (is_string($value)) </template> @endif
    </lazy>
@endif
