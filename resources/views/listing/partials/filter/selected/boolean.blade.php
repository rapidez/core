<template v-else-if="item.filter.input === 'boolean'">
    <template v-if="refinement.label == '1'">@lang('Yes')</template>
    <template v-if="refinement.label == '0'">@lang('No')</template>
</template>
