<script>
export default {
    props: {
        initialItems: {
            type: Array,
            default: () => [],
        },
    },

    data: () => ({
        live: false,
    }),

    render() {
        return this.$slots.default(this)
    },

    computed: {
        items() {
            return this.live ? [] : this.initialItems
        },
    },

    methods: {
        setLive() {
            this.live = true
        },
        sendEvent() {},
    },
}
</script>
