@props(['value', 'title' => false, 'field' => 'sku', 'snapshot' => true])
@slots(['items'])

{{--
Examples:
<x-rapidez::productlist :value="['MS04', 'MS05', 'MS09']"/>
<x-rapidez::productlist value="productIds" field="entity_id"/>
<x-rapidez::productlist :value="false" filter-query-string="sku:MS04,MS05,MS09"/>
<x-rapidez::productlist :value="false" v-bind:base-filters="() => [{dslQuery}}]"/>
<x-rapidez::productlist value="cart.items" :snapshot="false"/> (no SSR snapshot, for example when it depends on the visitor)
--}}

@if (!is_iterable($value) || count($value))
    @php
        // A string value is evaluated in the browser and probably depends on the page, like the related products.
        $snapshotId = config('rapidez.ssr.enabled') && config('rapidez.ssr.productlists') && $snapshot
            ? app(\Rapidez\Core\Search\ListingSnapshotStore::class)->id('productlist', $value, $field, $title, $attributes->getAttributes(), (string) ($before ?? ''), (string) $items, (string) ($after ?? ''), is_string($value) ? request()->getPathInfo() : null)
            : null;
        $snapshotParts = $snapshotId ? app(\Rapidez\Core\Search\ListingSnapshotStore::class)->get($snapshotId) : [];
    @endphp

    @if ($snapshotParts) <div> @endif
    <lazy v-slot="{ intersected }">
        @if (is_string($value)) <template v-if="{{ $value }}.length"> @endif
            <listing
                {{ $attributes }}
                v-if="intersected"
                v-slot="listingSlotProps"
                v-cloak
                @if ($snapshotId) snapshot-id="{{ $snapshotId }}" @endif
                @if ($snapshotParts) v-bind:has-snapshot="true" @endif
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

                        <ais-hits
                            v-slot="{ items, sendEvent }"
                            v-bind:transform-items="listingSlotProps.transformItems"
                            v-show="listingSlotProps.rendered"
                            v-bind="listingSlotProps.snapshotAttributes()"
                        >
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
        @if (is_string($value)) </template> @endif
    </lazy>
    @if ($snapshotParts)
        {{-- After the lazy component, so that doesn't move when the snapshot is replaced. --}}
        <x-rapidez::listing-snapshot :id="$snapshotId"/>
        </div>
    @endif
@endif
