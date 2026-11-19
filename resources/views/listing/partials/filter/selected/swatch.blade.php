<template v-else-if="item.filter.super">
    @{{ htmlDecode(refinement.swatch?.label ?? refinement.label) }}
</template>
