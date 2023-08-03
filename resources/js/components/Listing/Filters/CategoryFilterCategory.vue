<template>
    <li :class="classes.li">
        <label :class="[classes.label, value === category.id ? classes.active : '']">
            <input
                type="radio"
                name="category"
                :checked="value === category.id"
                :value="category.id"
                v-on:change="setQuery({ value: category.id })"
                class="hidden"
            />
            <span :class="classes.span">{{ category.label }}</span>
            <small :class="classes.small" v-if="category.doc_count">({{ category.doc_count }})</small>
        </label>

        <ul :class="classes.ul" v-if="category.children">
            <template v-for="child in category.children">
                <category-filter-category :category="child" :key="child.key" :value="value" :set-query="setQuery" :classes="classes" />
            </template>
        </ul>
    </li>
</template>

<script>
export default {
    props: {
        category: Object,
        value: String,
        setQuery: Function,
        classes: {
            type: Object,
            default: function () {
                return {
                    li: '',
                    ul: 'pl-3',
                    label: 'block',
                    active: 'font-semibold',
                    span: '',
                    small: '',
                }
            },
        },
    },
}
</script>
