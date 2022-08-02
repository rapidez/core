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
                .catch(this.errorHandler)
                .then(() => this.refreshCart())
            },

            remove(item) {
                this.magentoCart('delete', 'items/' + item.item_id)
                    .then((response) => {
                        this.$root.$emit('cart-remove', item)
                        Notify(item.name + ' ' + window.config.translations.cart.remove, 'info')
                    })
                    .catch(this.errorHandler)
                    .then(() => this.refreshCart())
            },

            errorHandler(error) {
                if (!this.expiredCartCheck(error)) {
                    Notify(error.response.data.message, 'error', error.response.data?.parameters)
                }
            }
        },
        created() {
            this.$root.$on('refresh-cart', this.refreshCart);

            this.$root.$on('clear-cart', (keys) => {
                this.clearCart(keys)
            });
        }
    }
</script>
