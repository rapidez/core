import { useLocalStorage } from '@vueuse/core'

/**
 * @typedef {Object} SearchHistory
 * {
 *  [query: string]: {
 *    count: number,
 *    lastSearched: string,
 *    hits: number,
 * }
 */
export const searchHistory = useLocalStorage('search_history', {})

export const addQuery = (query, metadata = {}) => {
    query = query.toLowerCase()
    Vue.set(searchHistory.value, query, {
        ...searchHistory.value[query],
        count: (searchHistory.value[query]?.count || 0) + 1,
        lastSearched: new Date().toISOString(),
        ...metadata,
    })
}

export const removeQuery = (query) => {
    if (searchHistory.value[query]) {
        delete searchHistory.value[query]
    }
}

export default () => searchHistory
