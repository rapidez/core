<script>
export default {
    inject: { instantSearchInstance: { from: '$_ais_instantSearchInstance' } },

    props: {
        parent: Object,
        attribute: Object,
        id: Number,
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            option: undefined,
        }
    },

    mounted() {
        this.option = this.refinement
    },

    methods: {
        isDisabled(optionId) {
            this.parent.disabledOptions['super_' + this.attribute.code].includes(optionId)
        },
    },

    computed: {
        options() {
            return this.parent.getOptions(this.attribute.code)
        },

        superRefinements() {
            let disjunctiveFacetsRefinements = this.instantSearchInstance.helper.state.disjunctiveFacetsRefinements
            return Object.fromEntries(
                Object.entries(disjunctiveFacetsRefinements)
                    .filter(([key, value]) => key.startsWith('super_') && value.length > 0)
                    .map(([key, value]) => [key.replace('super_', ''), value]),
            )
        },

        refinement() {
            let refinement = this.superRefinements[this.attribute.code]
            if (refinement?.length != 1) {
                return undefined
            }

            return refinement[0]
        },
    },

    watch: {
        option() {
            Vue.set(this.parent.options, this.id, this.option)
        },

        refinement() {
            this.option = this.refinement
        },
    },
}
</script>
