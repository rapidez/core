<script>
    import GetCart from './../Cart/mixins/GetCart'

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