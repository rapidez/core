<script>
    import GetCart from './../Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],
        data: () => ({
            toggle: false
        }),

        render() {
            return this.$scopedSlots.default({
                cart: this.cart,
                hasItems: this.hasItems,
                changeQty: this.changeQty,
                remove: this.remove,
                toggle: this.toggle,
                toggleCart: this.toggleCart,
                close: this.close
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
                .catch((error) => alert(error.response.data.message))
            },

            toggleCart() {
                this.toggle = !this.toggle
            },

            close() {
                this.toggle = false
            },

            remove(item) {
                this.magentoCart('delete', 'items/' + item.item_id)
                    .then((response) => this.refreshCart())
                    .catch((error) => alert(error.response.data.message))
            },
        }
    }
</script>
