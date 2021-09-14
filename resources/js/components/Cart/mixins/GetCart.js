export default {
    methods: {
        getCart() {
            if (this.$root.cart === null && localStorage.graphql_cart) {
                this.$root.cart = JSON.parse(localStorage.graphql_cart).cart
            }

            return this.$root.cart
        },

        async refreshCart() {
            this.$root.$emit('refresh-cart')
        },

        async getMask() {
            if (!localStorage.mask) {
                try {
                    var response = this.$root.user
                        ? await magentoUser.post('carts/mine')
                        : await magento.post('guest-carts')
                } catch (error) {
                    Notify(window.config.translations.errors.wrong, 'error')
                }

                if (response !== undefined && response.data) {
                    this.$root.mask = response.data
                }
            }
        },

        async linkUserToCart() {
            await magentoUser.put('guest-carts/'+this.$root.mask, {
                customerId: this.$root.user.id,
                storeId: config.store
            }).catch((error) => {
                Notify(error.response.data.message, 'warning')
            })
        }
    },

    computed: {
        cart: function () {
            return this.getCart()
        },

        hasItems: function () {
            return this.cart && this.cart.items && Object.keys(this.cart.items).length
        },

        hasVirtualItems: function () {
            return Object.values(this.cart.items).filter((item) => item.__typename == 'DownloadableCartItem').length
        },

        hasOnlyVirtualItems: function () {
            return this.hasVirtualItems === this.hasItems
        },

        cartCrossells: function () {
            let crossellSet = new Set()

            for (let item of Object.values(this.cart.items ? this.cart.items : [])) {
                for (let crossell_product of item.product.crosssell_products) {
                    crossellSet.add(crossell_product.id)
                }
            }

            return [...crossellSet]
        }
    },
}
