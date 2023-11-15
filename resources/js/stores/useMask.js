import { useLocalStorage } from '@vueuse/core'
import { user } from './useUser'

export const mask = useLocalStorage('mask', '')

export const refresh = async function () {
    try {
        // TOOD: Are we also going to migrate this to GraphQL?
        var response = user?.value?.id ? await magentoUser.post('carts/mine') : await magento.post('guest-carts')
    } catch (error) {
        Notify(window.config.translations.errors.wrong, 'error')
        console.error(error)

        return false
    }

    if (response === undefined || !response.data) {
        return false
    }

    mask.value = response.data

    return true
}

export const clear = async function () {
    mask.value = ''
}

export default () => mask
