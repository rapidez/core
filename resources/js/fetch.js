import { addFetch } from './stores/useFetches.js'
import { token } from './stores/useUser'

export class FetchError extends Error {
    constructor(message, response) {
        super(message)
        this.response = response
        this.name = this.constructor.name
    }
}
window.FetchError = FetchError

export class GraphQLError extends FetchError {
    constructor(errors, response) {
        super(errors[0].message, response)
        this.errors = errors
    }
}
window.GraphQLError = GraphQLError

export class SessionExpired extends FetchError {
    constructor(message, response) {
        super(message, response)
        this.name = this.constructor.name
    }
}
window.SessionExpired = SessionExpired

export const rapidezFetch = (window.rapidezFetch = ((originalFetch) => {
    return (...args) => {
        const result = originalFetch.apply(this, args)
        addFetch(result)

        return result
    }
})(fetch))

export const rapidezAPI = (window.rapidezAPI = async (method, endpoint, data = {}, options = {}) => {
    let response = await rapidezFetch(window.url('/api/' + endpoint.replace(/^\/+/, '')), {
        method: method.toUpperCase(),
        headers: Object.assign(
            {
                Store: window.config.store_code,
                Authorization: token.value ? `Bearer ${token.value}` : null,
                'Content-Type': 'application/json',
                'X-CSRF-Token': window.app.csrfToken,
            },
            options?.headers || {},
        ),
        body: Object.keys(data).length ? JSON.stringify(data) : null,
    })

    if (!response.ok) {
        throw new FetchError(window.config.translations.errors.wrong, response)
    }

    let responseData = await response.text()

    try {
        return JSON.parse(responseData)
    } catch (e) {
        return responseData
    }
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
    // You can't call response.json() twice, in case of errors we pass our clone instead which hasn't been read.
    let responseClone = response.clone()

    if (!response.ok && !response.headers?.get('content-type')?.includes('application/json')) {
        throw new FetchError(window.config.translations.errors.wrong, responseClone)
    }

    let data = await response.json()

    if (data?.errors) {
        console.error(data.errors)

        data?.errors?.forEach((error) => {
            if (error?.extensions?.category !== 'graphql-authorization') {
                return
            }

            if (options?.notifyOnError ?? true) {
                Notify(window.config.translations.errors.session_expired)
            }

            if (options?.redirectOnExpiration ?? true) {
                window.app.$emit('logout', { redirect: '/login' })
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired, responseClone)
            }
        })

        throw new GraphQLError(data.errors, responseClone)
    }

    if (!response.ok) {
        throw new FetchError(window.config.translations.errors.wrong, responseClone)
    }

    return data
})

export const magentoAPI = (window.magentoAPI = async (
    method,
    endpoint,
    data = {},
    options = {
        headers: {},
        redirectOnExpiration: true,
        notifyOnError: true,
    },
) => {
    let response = await rapidezFetch(config.magento_url + '/rest/' + window.config.store_code + '/V1/' + endpoint.replace(/^\/+/, ''), {
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
})
