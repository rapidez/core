@props(['id' => null, 'part' => 'default', 'routed' => null])
@aware(['snapshot' => null])

{{--
Renders (a part of) the SSR snapshot of a listing until it's replaced by the real listing.
The id defaults to the "snapshot" of the parent listing, so within a listing this is enough:
<x-rapidez::listing-snapshot part="products"/>
Mark the content to capture as that part within the listing with:
v-bind="listingSlotProps.snapshotAttributes('products')"
Outside of the listing, pass the id and whether the listing is routed (like the category listing):
<x-rapidez::listing-snapshot id="category-123" part="products" :routed="true"/>
--}}

@php
    // Only a string, the productlist uses a boolean "snapshot" prop. The listing component
    // is always routed, so the filters, sorting, etc. in the url are taken into account.
    if (! $id && is_string($snapshot)) {
        $id = $snapshot;
        $routed ??= true;
    }

    $html = $id ? app(\Rapidez\Core\Search\ListingSnapshotStore::class)->part($id, $part, (bool) $routed) : null;
@endphp

@if ($html)
    <listing-snapshot snapshot-id="{{ $id }}">
        <div {{ $attributes->merge(['data-testid' => 'listing-ssr']) }} v-pre>{!! $html !!}</div>
    </listing-snapshot>
@endif
