export default {
    methods: {
        getCart() {
            if (this.$root.cart === null && localStorage.cart) {
                this.$root.cart = JSON.parse(localStorage.cart)
            }

            return this.$root.cart
        },

        clearCart(keys = []) {
            keys.push('cart')
            keys.push('mask')

            Object.values(keys).forEach((key) => {
                localStorage.removeItem(key)
            })

            this.clearAdresses()

            window.app.cart = null
        },

        clearAdresses() {
            Object.keys(localStorage).forEach((key) => {
                if (!key.startsWith('shipping_') && !key.startsWith('billing_')) {
                    return
                }
                localStorage.removeItem(key)
            })
        },

        async refreshCart() {
            await this.getMask()

            if (localStorage.mask) {
                try {
                    let response = await axios.get(window.url('/api/cart/' + (localStorage.token ? localStorage.token : localStorage.mask)))
                    localStorage.cart = JSON.stringify(response.data)
                    window.app.cart = response.data
                    window.app.$emit('cart-refreshed')
                } catch (error) {
                    if (error.response.status == 404) {
                        localStorage.removeItem('mask')
                    }
                    Notify(window.config.translations.errors.wrong, 'warning')
                }
            }
        },

        async getMask() {
            if (!localStorage.mask) {
                try {
                    var response = window.app.user ? await magentoUser.post('carts/mine') : await magento.post('guest-carts')
                } catch (error) {
                    Notify(window.config.translations.errors.wrong, 'error')
                }

                if (response !== undefined && response.data) {
                    localStorage.mask = response.data
                }
            }
        },

        async linkUserToCart() {
            await magentoUser
                .put('guest-carts/' + localStorage.mask, {
                    customerId: window.app.user.id,
                    storeId: config.store,
                })
                .catch((error) => {
                    Notify(error.response.data.message, 'warning')
                })
        },

        expiredCartCheck(error) {
            if (error.response.data?.parameters?.fieldName == 'quoteId' || error.response.status === 404) {
                localStorage.removeItem('mask')
                localStorage.removeItem('cart')
                Notify(window.config.translations.errors.cart_expired, 'error')
                return true
            }
        },
    },

    created() {
        this.$root.$on('logout', () => this.clearCart())
    },

    computed: {
        cart: function () {
            return this.getCart()
        },

        hasItems: function () {
            return this.cart && this.cart.items && Object.keys(this.cart.items).length
        },

        hasVirtualItems: function () {
            return Object.values(this.cart.items).filter((item) => item.type == 'downloadable').length
        },

        hasOnlyVirtualItems: function () {
            return this.hasVirtualItems === this.hasItems
        },
    },
}
