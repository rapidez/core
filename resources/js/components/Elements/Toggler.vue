<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                isOpen: this.isOpen,
                toggle: this.toggle,
                close: this.close
            })
        },
        props: ['openDefault'],
        data: () => ({
            isOpen: false,
        }),
        mounted() {
            this.isOpen = this.openDefault ?? false;
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
