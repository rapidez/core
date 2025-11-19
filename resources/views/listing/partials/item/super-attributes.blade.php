<div v-for="(superAttribute, superAttributeId) in item.super_attributes" class="mt-2">
    <template v-if="superAttribute.additional_data?.swatch_input_type === 'visual'">
        @include('rapidez::listing.partials.item.super-attributes.visual-swatch')
    </template>
    <template v-else-if="superAttribute.additional_data?.swatch_input_type === 'text'">
        @include('rapidez::listing.partials.item.super-attributes.text-swatch')
    </template>
    <template v-else>
        @include('rapidez::listing.partials.item.super-attributes.drop-down')
    </template>
</div>
