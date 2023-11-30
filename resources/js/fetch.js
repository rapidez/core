// TODO: Search and replace: axios, magento and magentoUser
// And migrate them all to rapidezAPI, magentoGraphQL and magentoAPI

class SessionExpired extends Error {
    constructor(message) {
        super(message);
        this.name = this.constructor.name;
    }
}

window.rapidezFetch = (originalFetch => {
    return (...args) => {
        if (window.app.$data) {
            window.app.$data.loadingCount++
        }
        const result = originalFetch.apply(this, args);
        return result.then(window.app.$data.loadingCount--);
    };
})(fetch);

window.rapidezAPI = async(endpoint) => {
    let response = await rapidezFetch(window.url('/api/' + endpoint))
    if (!response.ok) {
        throw new Error(window.config.translations.errors.wrong)
    }
    return await response.json()
}

window.magentoGraphQL = async (query, variables = {}, headers = {}, redirectOnExpiration = true) => {
    let response = await rapidezFetch(config.magento_url + '/graphql', {
        method: 'POST',
        headers: {
            'Store': window.config.store_code,
            'Authorization': window.app.loggedIn ? `Bearer ${localStorage.token}` : null,
            'Content-Type': 'application/json',
            ...headers
        },
        body: JSON.stringify({
            query: query,
            variables: variables,
        })
    })

    if (!response.ok) {
        throw new Error(window.config.translations.errors.wrong)
    }

    response = await response.json()

    if (response.errors) {
        console.error(response.errors)

        if (response.errors[0]?.extensions?.category === 'graphql-authorization') {
            if (redirectOnExpiration) {
                this.logout(window.url('/login'))
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired)
            }
        }

        throw new Error(response.errors[0].message)
    }

    return response
}

window.magentoAPI = async (endpoint, method = 'GET', data = {}, headers = {}, redirectOnExpiration = true) => {
    let response = await rapidezFetch(config.magento_url + '/rest/' + window.config.store_code + '/V1/' + endpoint, {
        method: method,
        headers: {
            'Authorization': window.app.loggedIn ? `Bearer ${localStorage.token}` : null,
            'Content-Type': 'application/json',
            ...headers
        },
        body: JSON.stringify(data)
    })

    response = await response.json()

    if (!response.ok) {
        if (response.status == 401) {
            if (redirectOnExpiration) {
                this.logout(window.url('/login'))
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired)
            }
        }

        throw new Error(window.config.translations.errors.wrong)
    }

    return response
}

