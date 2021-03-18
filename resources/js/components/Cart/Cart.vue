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
                crossSellProducts: this.crossSellProducts
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

            remove(item) {
                this.magentoCart('delete', 'items/' + item.item_id)
                    .then((response) => this.refreshCart())
                    .catch((error) => alert(error.response.data.message))
            },
            getCrossSellIds() {
                let ids = '';
                Object.entries(this.cart.items).forEach(([key, val]) => {
                    Object.values(val.cross_sell_ids.split(',')).forEach(id => {
                        if(!ids.includes(id)) {
                            ids += id+ ","
                        }
                    })
                })

                return ids.slice(1,-2).split(',')
            }
        },
        computed: {
            crossSellProducts: function () {
                return this.getCrossSellIds()
            }
        }
    }
</script>
