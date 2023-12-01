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

        cart.value = Object.values(response.data)[0];
    } catch (error) {
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
