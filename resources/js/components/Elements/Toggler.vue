<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                isOpen: this.isOpen,
                toggle: this.toggle,
                close: this.close
            })
        },
        props: {
            open: {
                type: Boolean,
                default: false
            }
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
            },

            close() {
                this.isOpen = false
            }
        }
    }
</script>
