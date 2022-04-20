<template>
    <li>
        <label class="block" v-bind:class="{ 'font-semibold' : value === category.id}" >
            <input type="radio" name="category" :checked="value === category.id" :value="category.id" v-on:change="setQuery({value: category.id})" class="hidden"/>
            <span>
                {{ category.label }}
            </span>
            <small v-if="category.doc_count">({{ category.doc_count }})</small>
        </label>

        <ul v-if="category.children">
            <template v-for="child in category.children">
                <category-filter-category :category="child" :key="child.key" :value="value" :set-query="setQuery"></category-filter-category>
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
    },
    data: () => ({
        //
    }),

    render() {
        return this.$scopedSlots.default({
            category: this.category
        });
    }
};
</script>
