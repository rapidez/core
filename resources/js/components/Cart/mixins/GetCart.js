import { useLocalStorage, useSessionStorage } from "@vueuse/core"

let mask = useLocalStorage('mask', null)

export default {
    methods: {
        getCart() {
            if (mask.value && !this.$root.cart?.entity_id) {
                this.refreshCart()
            }
            return this.$root.cart
        },

        clearCart(keys = []) {
            this.$root.cart = {};
            mask.value = null;

            Object.values(keys).forEach((key) => {
                useLocalStorage(key).value = null;
            })

            this.clearAdresses()

            this.$root.cart = null
        },

        clearAdresses() {
            this.$root.checkout.shipping_address.forEach(() => null);
            this.$root.checkout.billing_address.forEach(() => null);
        },

        async refreshCart() {
            await this.getMask()

            if (mask.value) {
                try {
                    let response = await axios.get('/api/cart/' + (localStorage.token ? localStorage.token : mask.value))
                    this.$root.cart = response.data
                    window.app.$emit('cart-refreshed')
                } catch (error) {
                    if (error.response.status == 404) {
                        mask.value = null
                    }
                    Notify(window.config.translations.errors.wrong, 'warning')
                }
            }
        },

        async getMask() {
            if (!mask.value) {
                try {
                    var response = window.app.user
                        ? await magentoUser.post('carts/mine')
                        : await magento.post('guest-carts')
                } catch (error) {
                    Notify(window.config.translations.errors.wrong, 'error')
                }

                if (response !== undefined && response.data) {
                    mask.value = response.data
                }
            }
        },

        async linkUserToCart() {
            await magentoUser.put('guest-carts/'+mask.value, {
                customerId: window.app.user.id,
                storeId: config.store
            }).catch((error) => {
                Notify(error.response.data.message, 'warning')
            })
        },

        expiredCartCheck(error) {
            if (error.response.data?.parameters?.fieldName == 'quoteId' || error.response.status === 404) {
                mask.value = null;
                this.$root.cart = null
                Notify(window.config.translations.errors.cart_expired, 'error')
                return true
            }
        }
    },

    created() {
        this.$root.$on('logout', () => this.clearCart());
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
