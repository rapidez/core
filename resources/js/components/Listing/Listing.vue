<script>
    import {
        ReactiveBase,
        ReactiveList,
        ReactiveComponent,
        SelectedFilters,
        MultiList,
        DynamicRangeSlider
    } from '@appbaseio/reactivesearch-vue'

    Vue.use(ReactiveBase)
    Vue.use(ReactiveList)
    Vue.use(ReactiveComponent)
    Vue.use(SelectedFilters)
    Vue.use(MultiList)
    Vue.use(DynamicRangeSlider)

    export default {
        props: {
            additionalFilters: {
                type: Array,
                default: () => []
            },
            additionalSorting: {
                type: Array,
                default: () => []
            }
        },

        data: () => ({
            loaded: false,
            attributes: [],
        }),

        render() {
            return this.$scopedSlots.default({
                loaded: this.loaded,
                filters: this.filters,
                sortOptions: this.sortOptions,
                reactiveFilters: this.reactiveFilters
            })
        },

        mounted() {
            if (localStorage.attributes) {
                this.attributes = JSON.parse(localStorage.attributes)
                this.loaded = true
                return;
            }

            axios.get('/api/attributes')
                 .then((response) => {
                    this.attributes = response.data
                    localStorage.attributes = JSON.stringify(this.attributes)
                    this.loaded = true
                 })
                 .catch((error) => {
                    Notify(window.config.errors.wrong, 'error')
                })
        },

        computed: {
            filters: function () {
                return window.sortBy(window.filter(this.attributes, function (attribute) {
                    return attribute.filter;
                }), 'position')
            },
            sortings: function () {
                return window.filter(this.attributes, function (attribute) {
                    return attribute.sorting;
                })
            },
            reactiveFilters: function () {
                return window.map(this.filters, function (filter) {
                    return filter.code;
                }).concat(this.additionalFilters);
            },
            sortOptions: function () {
                return [
                    {
                        label: window.config.translations.relevance,
                        dataField: '_score',
                        sortBy: 'desc'
                    }
                ].concat(window.flatMap(this.sortings, function (sorting) {
                    return window.map({
                        asc: window.config.translations.asc,
                        desc: window.config.translations.desc
                    }, function (directionLabel, directionKey) {
                        return {
                            label: window.config.translations.sorting?.[sorting.code]?.[directionKey] ?? sorting.name + ' ' + directionLabel,
                            dataField: sorting.code + (sorting.code != 'price' ? '.keyword' : ''),
                            sortBy: directionKey
                        }
                    })
                })).concat(this.additionalSorting)
            }
        }
    }
</script>
