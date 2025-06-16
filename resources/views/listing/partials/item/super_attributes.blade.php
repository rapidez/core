<div v-for="(superAttribute, superAttributeId) in item.super_attributes" class="mt-2 block">
    <template v-if="superAttribute.visual_swatch">
        @include('rapidez::listing.partials.item.super-attributes.visual_swatch')
    </template>
    <template v-else-if="superAttribute.text_swatch">
        @include('rapidez::listing.partials.item.super-attributes.text_swatch')
    </template>
    <template v-else>
        @include('rapidez::listing.partials.item.super-attributes.drop_down')
    </template>
</div>
