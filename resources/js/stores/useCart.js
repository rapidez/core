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

    await axios.post(config.magento_url + '/graphql', {
        query: `query getCart($cart_id: String!) { cart (cart_id: $cart_id) { ${config.queries.cart} } }`,
        variables: {
            cart_id: mask.value,
        }
    }, { headers: { Authorization: `Bearer ${token.value}` } })
        .then(async (response) => {
            if ('errors' in response.data) {
                throw new axios.AxiosError('Graphql Errors', null, response.config, response.request, response)
            }

            return response;
        })
        .then(async (response) => {
            cart.value = Object.values(response.data.data)[0];
        })
        .catch((error) => {
            if (!error?.response) {
                return error
            }

            checkResponseForExpiredCart(error.response);
            return error
        })

    return true;
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
            refresh()
        }

        return cartStorage.value
    },
    set(value) {
        cartStorage.value = value
        age = Date.now();
    },
})

// If mask gets added, updated or removed we should update the cart.
watch(mask, refresh)

export default () => cart
