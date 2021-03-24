<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                isOpen: this.isOpen,
                toggle: this.toggle,
                close: this.close
            })
        },

        data: () => ({
            isOpen: false,
        }),

        methods: {
            toggle() {
                this.isOpen = !this.isOpen
            },

            close() {
                this.isOpen = false
            }
        }
    }
</script>
