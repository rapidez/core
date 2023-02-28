import { useLocalStorage, useSessionStorage, StorageSerializers } from '@vueuse/core'
import { clear as clearCart } from './useCart'
import { computed, watch } from 'vue'

export const token = useLocalStorage('token', '')
const userStorage = useSessionStorage('user', {}, {serializer: StorageSerializers.object})

export const refresh = async function () {
    if (!token.value) {
        userStorage.value = {}
        return false
    }

    try {
        window.magentoUser.defaults.headers.common['Authorization'] = `Bearer ${token.value}`;
        let response = await magentoUser.get('customers/me')
        userStorage.value = response.data
    } catch (error) {
        if (error.response.status == 401) {
            token.value = '';
            return false
        }

        console.error(error);
        return false;
    }

    return true;
}

export const clear = async function () {
    token.value = ''
    userStorage.value = {}
    await clearCart();
}

export const user = computed({
    get() {
        if (token.value && !userStorage.value?.id) {
            refresh()
        }

        return userStorage.value
    },
    set(value) {
        userStorage.value = value
    }
})

// If token gets changed or emptied we should update the user.
watch(token, refresh);

export default ()=>(user)
