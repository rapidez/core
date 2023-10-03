import { useLocalStorage } from '@vueuse/core'
import { cart, refresh as refreshCart, clear as clearCart } from '../../../stores/useCart'
import { user } from '../../../stores/useUser'
import { mask, refresh as retrieveMask } from '../../../stores/useMask'

export default {
    methods: {
        getCart() {
            return cart.value
        },

        clearCart(keys = []) {
            clearCart()

            Object.values(keys).forEach((key) => {
                useLocalStorage(key).value = null
            })
        },

        async refreshCart() {
            return await refreshCart()
        },

        async getMask() {
            if (mask.value) {
                return mask.value
            }
            await retrieveMask()
            return mask.value
        },

        async linkUserToCart() {
            await magentoUser
                .put('guest-carts/' + mask.value, {
                    customerId: user.value?.id,
                    storeId: config.store,
                })
                .then(() => {
                    mask.value = cart.entity_id
                })
                .catch((error) => {
                    Notify(error.response.data.message, 'warning')
                })
        },

        expiredCartCheck(error) {
            if (error.response.data?.parameters?.fieldName == 'quoteId' || error.response.status === 404) {
                clearCart()
                Notify(window.config.translations.errors.cart_expired, 'error')
                return true
            }
        },
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
