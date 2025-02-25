{{-- TODO: Display swatches correctly here --}}

<template v-else-if="item.filter.super">
    @{{ refinement.swatch?.label ?? refinement.label }}
</template>
