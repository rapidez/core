<script>
export default {
    props: {
        snapshotId: {
            type: String,
            required: true,
        },
    },

    data: () => ({
        visible: true,
    }),

    mounted() {
        window.$on('listing:rendered', this.hide, { defer: false })
    },

    methods: {
        hide(snapshotId) {
            if (snapshotId === this.snapshotId) {
                this.visible = false
            }
        },
    },

    render() {
        return this.visible ? this.$slots.default?.() : null
    },
}
</script>
