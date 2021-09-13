<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],
        render() {
            return this.$scopedSlots.default({
                cart: this.cart,
                cartCrossells: this.cartCrossells,
                refreshCart: this.refreshCart,
            })
        },
    }
</script>
