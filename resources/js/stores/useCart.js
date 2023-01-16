import { useSessionStorage, StorageSerializers } from '@vueuse/core'
import { computed, watch } from '@vue/composition-api'
import { mask, clear as clearMask } from './useMask'

const cartStorage = useSessionStorage('cart', {}, {serializer: StorageSerializers.object})
let hasRefreshed = false;

export const refresh = async function () {
    hasRefreshed = true;
    if (!mask.value && !localStorage.token) {
        cartStorage.value = {}
        return false;
    }

    try {
        let response = await axios.get('/api/cart/' + (localStorage.token ? localStorage.token : mask.value))
        cartStorage.value = response.data
        window.app.$emit('cart-refreshed')
    } catch (error) {
        if (error.response.status == 404) {
            mask.value = null
        }

        Notify(window.config.translations.errors.wrong, 'warning')
        console.error(error)

        return false;
    }
}

export const clear = async function () {
    await clearMask()
    cartStorage.value = {}
}


export const cart = computed({
    get() {
        if (!cartStorage.value?.entity_id && mask.value) {
            refresh();
        }
        return cartStorage.value
    },
    set(value) {
        cartStorage.value = value
    }
})

// If mask gets added, updated or removed we should update the cart.
watch(mask, refresh);
// refresh the cart on first pageload after a while
window.setTimeout(() => window.requestIdleCallback(() => !hasRefreshed && refresh), 5000);

export default ()=>(cart)
