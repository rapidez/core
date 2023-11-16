import { useLocalStorage } from '@vueuse/core'
import { user } from './useUser'

export const mask = useLocalStorage('mask', '')

export const refreshMask = async function () {
    try {
        // TODO: Maybe make this generic? See: https://github.com/rapidez/core/pull/376
        // TODO: Maybe migrate to fetch? We don't need axios anymore?
        let response = await axios.post(config.magento_url + '/graphql', {
            query: 'mutation { createEmptyCart }'
        }, { headers: { Authorization: `Bearer ${localStorage.token}`, Store: config.store_code } })

        mask.value = response.data.data.createEmptyCart
    } catch (error) {
        Notify(window.config.translations.errors.wrong, 'error')
        console.error(error)
    }
}

export const clearMask = async function () {
    mask.value = ''
}

export default () => mask
