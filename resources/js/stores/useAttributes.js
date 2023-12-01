// TODO: In this file there is a lot of duplication compared to useSwatches. Can we improve that?
import { computedAsync, useLocalStorage } from '@vueuse/core'

export const attributesStorage = useLocalStorage('attributes', {})
let isRefreshing = false

export const refresh = async function () {
    if (isRefreshing) {
        console.debug('Refresh canceled, request already in progress...')
        return
    }

    try {
        isRefreshing = true
        attributesStorage.value = await window.rapidezAPI('attributes') || {}
        isRefreshing = false
    } catch (error) {
        isRefreshing = false
        console.error(error)
        Notify(window.config.translations.errors.wrong, 'error')
    }
}

export const clear = async function () {
    attributesStorage.value = {}
}

export const attributes = computedAsync(
    async () => {
        if (Object.keys(attributesStorage.value).length === 0) {
            await refresh()
        }

        return attributesStorage
    },
    attributesStorage.value,
    { lazy: true, shallow: false },
)

export default () => attributes
