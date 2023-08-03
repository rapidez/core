<script>
export default {
    render() {
        return this.$scopedSlots.default({
            isOpen: this.isOpen,
            toggle: this.toggle,
            close: this.close,
        })
    },
    props: {
        open: {
            type: Boolean,
            default: false,
        },
        callback: {
            type: Function,
        },
    },
    data: () => ({
        isOpen: false,
    }),
    mounted() {
        this.isOpen = this.open
    },
    methods: {
        toggle() {
            this.isOpen = !this.isOpen

            if (this.callback) {
                this.callback(this.isOpen)
            }
        },

        close() {
            this.isOpen = false
            if (this.callback) {
                this.callback(this.isOpen)
            }
        },
    },
}
</script>
