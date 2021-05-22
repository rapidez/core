export default {
    methods: {
        getCart() {
            if (this.$root.cart === null && localStorage.cart) {
                this.$root.cart = JSON.parse(localStorage.cart)
            }

            return this.$root.cart
        },

        async refreshCart() {
            await this.getMask()

            if (localStorage.mask) {
                try {
                    let response = await axios.get('/api/cart/' + (localStorage.token ? localStorage.token : localStorage.mask))
                    localStorage.cart = JSON.stringify(response.data)
                    this.$root.cart = response.data
                } catch (error) {
                    if (error.response.status == 404) {
                        localStorage.removeItem('mask')
                    }
                    Notify(window.config.translations.frontend.errors.wrong, 'warning')
                }
            }
        },

        async getMask() {
            if (!localStorage.mask) {
                try {
                    var response = this.$root.user
                        ? await magentoUser.post('carts/mine')
                        : await magento.post('guest-carts')
                } catch (error) {
                    Notify(window.config.translations.frontend.errors.wrong, 'error')
                }

                if (response !== undefined && response.data) {
                    localStorage.mask = response.data
                }
            }
        },

        async linkUserToCart() {
            await magentoUser.put('guest-carts/'+localStorage.mask, {
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
            return Object.values(this.cart.items).filter((item) => item.type == 'downloadable').length
        },

        hasOnlyVirtualItems: function () {
            return this.hasVirtualItems === this.hasItems
        }
    },
}
