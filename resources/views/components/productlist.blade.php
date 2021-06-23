@props(['value', 'title', 'field' => 'sku.keyword', 'size' => 6])

@if($value)
    <reactive-base v-cloak :app="config.es_prefix + '_products_' + config.store" :url="config.es_url">
        <reactive-list
            component-id="{{ md5(serialize($value)) }}"
            data-field="{{ $field }}"
            list-class="flex flex-wrap mt-5 -mx-1 overflow-hidden"
            :size="{{ $size }}"
            :default-query="function () { return { query: { terms: { '{{ $field }}': {!!
                is_array($value)
                    ? "['".implode("','", $value)."']"
                    : $value
            !!} } } } }"
        >
            @isset($title)
                <strong class="font-bold text-2xl mt-5" slot="renderResultStats">
                    @lang($title)
                </strong>
            @else
                <div slot="renderResultStats"></div>
            @endif

            <div slot="renderNoResults"></div>

            @include('rapidez::listing.partials.item', ['perLine' => $size])
        </reactive-list>
    </reactive-base>
@endif
