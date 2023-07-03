<script>
import { useWebWorkerFn } from '@vueuse/core'

export default {
    props: {
        aggregations: Object,
        setQuery: Function,
        value: String,
    },

    render() {
        return this.$scopedSlots.default({
            hasResults: this.hasResults,
            results: this.results,
        })
    },

    data: () => ({
        results: [],
    }),

    created() {
        this.processValue(this.value)
    },

    watch: {
        value: function (value) {
            this.processValue(value)
        },
        hasResults: async function (value) {
            if (!value) {
                return
            }

            this.createCategoryPaths()
        },
    },

    methods: {
        processValue(value) {
            if (!value) {
                this.setQuery({})
                return
            }

            this.setQuery({
                query: { match_phrase: { category_paths: value } },
                value: value,
            })
        },

        async createCategoryPaths() {
            if (!this.aggregations?.category_paths?.buckets?.length || !this.aggregations?.categories?.buckets?.length) {
                this.results = []
                return
            }

            // Calculating and unpacking the category structure is a heavy task.
            // Hand this down to a webworker thread to free the main thread.
            const { workerFn: getCategoryStructureWorker } = useWebWorkerFn((categories, allCategoryPaths, currentCategory) => {
                function getCategoryStructure(categories, allCategoryPaths, currentCategory) {
                    const lowestCategories = categories
                        .map((bucket) => {
                            const key = bucket.key.split(' /// ').pop()
                            const [id, label] = key.split('::')

                            return {
                                id: id,
                                key: bucket.key,
                                structure: bucket.key.split(' /// '),
                                label: label,
                                doc_count: bucket.doc_count,
                            }
                        })
                        .filter((bucket) =>
                            getCategoryPaths(allCategoryPaths, currentCategory).find((path) => path.key.includes(bucket.id))
                        )

                    return buildCategoryStructure(lowestCategories).children
                }

                function getCategoryPaths(allCategoryPaths, currentCategory) {
                    if (!currentCategory?.entity_id || !allCategoryPaths) {
                        return allCategoryPaths
                    }

                    // Get children of current category.
                    let categoryPaths = allCategoryPaths.filter(
                        (bucket) =>
                            bucket.key.includes(String(currentCategory.entity_id)) &&
                            bucket.key.at(-1) !== String(currentCategory.entity_id)
                    )

                    if (categoryPaths.length > 1) {
                        return categoryPaths
                    }

                    // No child categories, get siblings instead.
                    let parentCategoryId = categoryPaths[0].key.at(-2)
                    return allCategoryPaths.filter(
                        (bucket) => bucket.key.includes(parentCategoryId) && bucket.key.at(-1) !== parentCategoryId
                    )
                }

                function buildCategoryStructure(categories, results = { children: {} }) {
                    categories.map((category) => {
                        if (!category.structure.length) {
                            return
                        }

                        const key = category.structure.shift()
                        const [id, label] = key.split('::')

                        if (!results.children) {
                            results.children = {}
                        }

                        if (!results?.children?.hasOwnProperty(id)) {
                            results.children[id] = {
                                id: id,
                                key: key,
                                label: label,
                                structure: category.structure,
                                doc_count: category.doc_count,
                                children: {},
                            }
                        }

                        results.children[id] = buildCategoryStructure([category], results.children[id])
                    })

                    return results
                }

                return getCategoryStructure(categories, allCategoryPaths, currentCategory)
            })

            this.results = await getCategoryStructureWorker(
                this.aggregations.categories.buckets,
                this.aggregations.category_paths.buckets,
                this.currentCategory
            )
        },
    },

    computed: {
        hasResults: function () {
            return this.aggregations?.categories?.buckets?.length
        },

        currentCategory: function () {
            return config.category
        },
    },
}
</script>
