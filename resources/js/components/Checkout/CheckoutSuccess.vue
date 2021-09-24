<script>
    import GetCart from 'Vendor/rapidez/core/resources/js/components/Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],

        render() {
            return this.$scopedSlots.default()
        },

        created() {
            this.clearCart()
        }
    }
</script>
