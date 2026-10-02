<script>
export default {
    data: () => ({
        visible: true,
    }),

    mounted() {
        window.$on('listing:rendered', this.hide, { defer: false })
    },

    methods: {
        hide() {
            this.visible = false
        },
    },

    render() {
        return this.visible ? this.$slots.default?.() : null
    },
}
</script>
