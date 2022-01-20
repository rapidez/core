<script>
export default {
    props: {
        aggregations: Object,
    },

    data: () => ({
        //
    }),

    mounted() {
        //
    },

    render() {
        return this.$scopedSlots.default({
            hasResults: this.hasResults,
        })
    },

    methods: {
        method() {
            //
        }
    },

    computed: {
        hasResults: function () {
            // TODO: We need to check if we've results based on the
            // currenty category. There is a "v-if" on the anchors
            // which logic we also need here. But it also need
            // to work on the search results page!
            return this.aggregations.categories.buckets.length
        },
    }
};
</script>
