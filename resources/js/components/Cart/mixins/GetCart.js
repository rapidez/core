// TODO: It should be possible to remove this whole file?
import { useLocalStorage } from '@vueuse/core'
import { cart, clear as clearCart } from '../../../stores/useCart'
import { mask, refreshMask } from '../../../stores/useMask'
import { token, refresh as refreshUser } from '../../../stores/useUser'

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

        async getMask() {
            if (mask.value) {
                return mask.value
            }
            await refreshMask()
            return mask.value
        },

        async linkUserToCart() {
            await window.magentoGraphQL(
                'mutation ($cart_id: String!) { assignCustomerToGuestCart (cart_id: $cart_id) { ' + config.queries.cart + ' } }',
                { cart_id: mask.value }
            ).then((response) => this.updateCart([], response))
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
