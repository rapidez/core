@props(['id' => null, 'part' => 'default'])
@aware(['snapshot' => null])

{{--
Renders (a part of) the SSR snapshot of a listing until it's replaced by the real listing.
The id defaults to the "snapshot" of the parent listing, so within a listing this is enough:
<x-rapidez::listing-snapshot part="products"/>
Mark the content to capture as that part within the listing with:
v-bind="listingSlotProps.snapshotAttributes('products')"
--}}

@php($id ??= $snapshot)
@php($html = $id ? app(\Rapidez\Core\Search\ListingSnapshotStore::class)->part($id, $part) : null)

@if ($html)
    <listing-snapshot snapshot-id="{{ $id }}">
        <div {{ $attributes->merge(['data-testid' => 'listing-ssr']) }} v-pre>{!! $html !!}</div>
    </listing-snapshot>
@endif
