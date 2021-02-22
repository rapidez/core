<script>
    export default {
        props: {
            translations: {
                type: Object,
                default: () => ({
                    relevance: 'Relevance',
                    desc: 'desc',
                    asc: 'asc',
                })
            }
        },

        data: () => ({
            loaded: false,
            attributes: [],
            baseStyles: {},
        }),

        render() {
            return this.$scopedSlots.default({
                loaded: this.loaded,
                baseStyles: this.baseStyles,

                filters: this.filters,
                reactiveFilters: this.reactiveFilters,
                sortOptions: this.sortOptions,
            })
        },

        mounted() {
            if (sessionStorage.getItem('attributes')) {
                this.attributes = JSON.parse(sessionStorage.getItem('attributes'))
                this.loaded = true
                return;
            }

            axios.get('/api/attributes')
                 .then((response) => {
                    this.attributes = response.data
                    sessionStorage.setItem('attributes', JSON.stringify(this.attributes))
                    this.loaded = true
                 })
                 .catch((error) => {
                    alert('Something went wrong')
                })
        },

        computed: {
            filters: function () {
                return _.sortBy(_.filter(this.attributes, function (attribute) {
                    return attribute.filter;
                }), 'position')
            },
            sortings: function () {
                return _.filter(this.attributes, function (attribute) {
                    return attribute.sorting;
                })
            },
            reactiveFilters: function () {
                return _.map(this.filters, function (filter) {
                    return filter.code;
                }).concat(['category', 'searchterm']);
            },
            sortOptions: function () {
                let self = this
                return [
                    {
                        label: this.translations.relevance,
                        dataField: '_score',
                        sortBy: 'desc'
                    }
                ].concat(_.flatMap(this.sortings, function (sorting) {
                    return _.map({
                        asc: self.translations.asc,
                        desc: self.translations.desc
                    }, function (directionLabel, directionKey) {
                        return {
                            label: sorting.name + ' ' + directionLabel,
                            dataField: sorting.code + (sorting.code != 'price' ? '.keyword' : ''),
                            sortBy: directionKey
                        }
                    })
                }))
            }
        }
    }
</script>
