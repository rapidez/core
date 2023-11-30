import { StorageSerializers, useLocalStorage } from '@vueuse/core'
import { computed, watch } from 'vue'
import { mask, clearMask } from './useMask'
import { token, refresh as refreshUser } from './useUser'

const cartStorage = useLocalStorage('cart', {}, { serializer: StorageSerializers.object })
export let age = 0;

export const refresh = async function (force = false) {
    if (!mask.value) {
        cartStorage.value = {}
        return false;
    }

    if (!force && Date.now() - age < 5000) {
        // The latest cart info is 5 seconds old, we do not need to refresh.
        return;
    }

    age = Date.now();

    try {
        let response = await window.magentoGraphQL(
            `query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ${config.queries.cart} } }`,
            { cart_id: mask.value }
        )

        if ('errors' in response.data) {
            // TODO: Double check
            throw new Error('Graphql Errors', null, response.config, response.request, response)
        }

        cart.value = Object.values(response.data)[0];
    } catch (error) {
        // TODO: Double check
        checkResponseForExpiredCart(error.response);
        console.error(error)
    }
}

export const clear = async function () {
    await clearAddresses()
    await clearMask()
    await refresh()
}

export const clearAddresses = async function () {
    useLocalStorage('billing_address').value = null
    useLocalStorage('shipping_address').value = null
}

// TODO: Check, is this the way to go? And the place where this should be checked?
export const checkResponseForExpiredCart = async function(response) {
    if (
        response?.data?.parameters?.fieldName == 'quoteId' ||
        response?.status === 404 ||
        response.data.errors?.some(error =>
            error.extensions.category === 'graphql-no-such-entity' &&
            error.path.some(path => ['cart', 'customerCart', 'assignCustomerToGuestCart', 'mergeCarts', 'addProductsToCart', 'removeItemFromCart', 'updateCartItems'].includes(path))
        )
     ) {
         Notify(window.config.translations.errors.cart_expired, 'error')
         clear()
         if (token.value !== undefined) {
             // If the cart has expired, check if the session is not expired
             refreshUser()
         }

         return true
     }

     return false;
}

export const cart = computed({
    get() {
        if (!cartStorage.value?.id && mask.value) {
            // TODO: Check as we don't want this all the time.
            // refresh()
        }

        return cartStorage.value
    },
    set(value) {
        cartStorage.value = value
        age = Date.now();
    },
})

// TODO: Check as we don't want this all the time.
// If mask gets added, updated or removed we should update the cart.
// watch(mask, refresh)

export default () => cart
