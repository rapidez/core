import { print } from 'graphql'
import { token } from './stores/useUser'
import combineQuery from 'graphql-combine-query'
import { gql } from 'graphql-tag'

class FetchError extends Error {
    constructor(message, response) {
        super(message)
        this.response = response
        this.name = this.constructor.name
    }
}
window.FetchError = FetchError

class SessionExpired extends FetchError {
    constructor(message, response) {
        super(message, response)
        this.name = this.constructor.name
    }
}
window.SessionExpired = SessionExpired

window.rapidezFetch = ((originalFetch) => {
    return (...args) => {
        if (window.app.$data) {
            window.app.$data.loadingCount++
        }
        const result = originalFetch.apply(this, args)
        return result.finally((...args) => {
            if (window.app.$data) {
                window.app.$data.loadingCount--
            }

            return args
        })
    }
})(fetch)

window.rapidezAPI = async (method, endpoint, data = {}, options = {}) => {
    let response = await rapidezFetch(window.url('/api/' + endpoint), {
        method: method.toUpperCase(),
        headers: Object.assign(
            {
                Store: window.config.store_code,
                Authorization: token.value ? `Bearer ${token.value}` : null,
                'Content-Type': 'application/json',
            },
            options?.headers || {},
        ),
        body: Object.keys(data).length ? JSON.stringify(data) : null,
    })

    if (!response.ok) {
        throw new FetchError(window.config.translations.errors.wrong, response)
    }

    return await response.json()
}

export const combineGraphqlQueries = window.combineGraphqlQueries = function (queries) {
    if (!Array.isArray(queries)) {
        queries = [...arguments];
    }

    // Transform all queries into a gql object
    queries = queries.map((query) => typeof query === 'string' ? gql(query) : query);

    const name = queries.reduce((str, query) => str + query.definitions.reduce((name, definition) => name + definition.name.value, ''), '');
    const { document } = queries.reduce((newQuery, query) => {
        return newQuery.add(query)
    }, combineQuery(name));

    return print(document);
}

let pendingQuery = null;
let pendingData = [];

export const combiningGraphQL = (window.combiningGraphQL = async (query, variables) => {
    if (!pendingQuery) {
        pendingQuery = new Promise((resolve, reject) => window.setTimeout(() => {
            const query = combineGraphqlQueries(pendingData.map(([query, variables]) => query))
            const variables = pendingData.reduce((combinedVariables, [query, variables]) => {return {...combinedVariables, ...variables};}, {});

            pendingData = [];
            pendingQuery = null;

            magentoGraphQL(query, variables).then(resolve)
        }, 5))
    }

    pendingData.push([query, variables])
    return pendingQuery
})

export const magentoGraphQL = (window.magentoGraphQL = async (
    query,
    variables = {},
    options = {
        headers: {},
        redirectOnExpiration: true,
        notifyOnError: true,
    },
) => {
    let response = await rapidezFetch(config.magento_url + '/graphql', {
        method: 'POST',
        headers: {
            Store: window.config.store_code,
            Authorization: token.value ? `Bearer ${token.value}` : null,
            'Content-Type': 'application/json',
            ...(options?.headers || {}),
        },
        body: JSON.stringify({
            query: query,
            variables: variables,
        }),
    })

    if (!response.ok) {
        throw new FetchError(window.config.translations.errors.wrong, response)
    }

    // You can't call response.json() twice, in case of errors we pass our clone instead which hasn't been read.
    let responseClone = response.clone()
    let data = await response.json()

    if (data.errors) {
        console.error(data.errors)

        if (data.errors[0]?.extensions?.category === 'graphql-authorization') {
            if (options?.notifyOnError ?? true) {
                Notify(window.config.translations.errors.session_expired)
            }

            if (options?.redirectOnExpiration ?? true) {
                window.app.$emit('logout', { redirect: '/login' })
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired, responseClone)
            }
        }

        throw new FetchError(data.errors[0].message, responseClone)
    }

    return data
}

window.magentoAPI = async (
    method,
    endpoint,
    data = {},
    options = {
        headers: {},
        redirectOnExpiration: true,
        notifyOnError: true,
    },
) => {
    let response = await rapidezFetch(config.magento_url + '/rest/' + window.config.store_code + '/V1/' + endpoint, {
        method: method.toUpperCase(),
        headers: {
            Authorization: token.value ? `Bearer ${token.value}` : null,
            'Content-Type': 'application/json',
            ...(options?.headers || {}),
        },
        body: Object.keys(data).length ? JSON.stringify(data) : null,
    })

    if (!response.ok) {
        if ([401, 404].includes(response.status)) {
            if (options?.notifyOnError ?? true) {
                Notify(window.config.translations.errors.session_expired)
            }

            if (options?.redirectOnExpiration ?? true) {
                window.app.$emit('logout', { redirect: '/login' })
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired, response)
            }
        }

        if (options?.notifyOnError ?? true) {
            Notify(window.config.translations.errors.wrong)
        }

        throw new FetchError(window.config.translations.errors.wrong, response)
    }

    let responseData = await response.json()

    return responseData
}
