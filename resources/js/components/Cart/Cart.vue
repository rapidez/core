<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],
        render() {
            return this.$scopedSlots.default({
                cart: this.cart,
                hasItems: this.hasItems,
                changeQty: this.changeQty,
                remove: this.remove,
            })
        },
        methods: {
            changeQty(item) {
                this.magentoCart('put', 'items/' + item.item_id, {
                    cartItem: {
                        quote_id: localStorage.mask,
                        qty: item.qty
                    }
                })
                .then((response) => this.refreshCart())
                .catch((error) => Notify(error.response.data.message, 'error'))
            },

            remove(item) {
                this.magentoCart('delete', 'items/' + item.item_id)
                    .then((response) => {
                        this.refreshCart()
                        Notify(item.name + ' ' + window.config.translations.cart.remove, 'info')
                    })
                    .catch((error) => Notify(error.response.data.message, 'error'))
            },
        }
    }
</script>
