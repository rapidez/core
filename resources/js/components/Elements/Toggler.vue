<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                isOpen: this.isOpen,
                toggle: this.toggle,
                away: this.away
            })
        },

        data: () => ({
            isOpen: false,
        }),

        methods: {
            toggle() {
                this.isOpen = !this.isOpen
            },

            away() {
                this.isOpen = false
            }
        }
    }
</script>
