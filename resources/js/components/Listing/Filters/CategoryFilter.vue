<script>
export default {
    props: {
        aggregations: Object,
        currentCategory: Object,
        setQuery: Function,
        value: String,
    },

    data: () => ({
        //
    }),

    render() {
        return this.$scopedSlots.default({
            hasResults: this.hasResults,
            results: this.results,
        })
    },
    watch: {
        value: function (value) {
            if (!value) {
                this.setQuery({});
            }
        }
    },
    methods: {
        getCategoryPaths() {
            const allCategoryPaths = this.aggregations.category_paths.buckets;

            if (!this.currentCategory?.entity_id)
            {
                return allCategoryPaths;
            }

            // Get children of current category.
            let categoryPaths = allCategoryPaths.filter(bucket => bucket.key.includes(String(this.currentCategory.entity_id))&& bucket.key.at(-1) !== String(this.currentCategory.entity_id));

            if (categoryPaths.length > 1) {
                return categoryPaths;
            }

            // No child categories, get siblings instead.
            let parentCategoryId = categoryPaths[0].key.at(-2);
            return allCategoryPaths.filter(bucket => bucket.key.includes(parentCategoryId) && bucket.key.at(-1) !== parentCategoryId);
        }
    },

    computed: {
        hasResults: function () {
            // TODO: We need to check if we've results based on the
            // currenty category. There is a "v-if" on the anchors
            // which logic we also need here. But it also need
            // to work on the search results page!
            return this.aggregations?.categories?.buckets?.length;
        },
        results: function () {
            if (
                !this.aggregations?.category_paths?.buckets?.length ||
                !this.aggregations?.categories?.buckets?.length
            ) {
                return [];
            }

            return this.aggregations.categories.buckets.map((bucket => {
                let [id, label] = bucket.key.split('::');
                return {
                    id: id,
                    key: bucket.key,
                    label: label.split(' /// ').pop(),
                    doc_count: bucket.doc_count
                };
            })).filter(bucket => this.getCategoryPaths().find(path => path.key.includes(bucket.id)));
        },
    }
};
</script>
