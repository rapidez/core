<div v-if="items && items.length" class="overflow-hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 -mx-5 -mb-px *:border-b">
        <template v-for="(item, count) in items" v-bind:key="item.entity_id">
            @include('rapidez::listing.partials.item')
        </template>
    </div>
</div>
<div v-else>
    @include('rapidez::listing.partials.no-results')
</div>
