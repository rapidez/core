import { useLocalStorage } from '@vueuse/core'
import { token, user } from './useUser'

export const mask = useLocalStorage('mask', '')

export const refreshMask = async function () {
    try {
        let response = await window.magentoGraphQL('mutation { createEmptyCart }')
        mask.value = response.data.createEmptyCart
    } catch (error) {
        Notify(window.config.translations.errors.wrong, 'error')
        console.error(error)
    }
}

export const clearMask = async function () {
    mask.value = ''
}

export default () => mask
