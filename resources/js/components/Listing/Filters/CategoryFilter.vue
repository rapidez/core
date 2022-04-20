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
        });
    },

    created() {
        this.processValue(this.value);
    },

    watch: {
        value: function (value) {
            this.processValue(value);
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
        },

        processValue(value) {
            if (!value) {
                this.setQuery({});
                return;
            }

            this.setQuery({
                query: { match_phrase: { 'category_ids': value } },
                value: value
            });
        },

        buildCategoryStructure(categories, results = {children: {}}) {
            for (const category of categories) {
                if (!category.structure.length) {
                    continue;
                }
                let key = category.structure.shift();
                let [id, label] = key.split('::');

                if(!results.children) {
                    results.children = {};
                }
                if (!results?.children?.hasOwnProperty(id)) {
                    results.children[id] = {
                        id: id,
                        key: key,
                        label: label,
                        structure: category.structure,
                        doc_count: category.doc_count,
                        children: {}
                    };
                }

                results.children[id] = this.buildCategoryStructure([category], results.children[id]);
            }
            return results;
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

            const lowestCategories = this.aggregations.categories.buckets.map((bucket => {
                const key = bucket.key.split(' /// ').pop();
                const [id, label] = key.split('::');
                return {
                    id: id,
                    key: bucket.key,
                    structure: bucket.key.split(' /// '),
                    label: label,
                    doc_count: bucket.doc_count
                };
            })).filter(bucket => this.getCategoryPaths().find(path => path.key.includes(bucket.id)));

            return this.buildCategoryStructure(lowestCategories).children;
        },
    }
};
</script>
