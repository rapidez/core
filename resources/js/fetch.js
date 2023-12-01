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

window.magentoGraphQL = async (query, variables = {}, options = {
    headers: {},
    redirectOnExpiration: true,
    notifyOnError: true,
}) => {
    let response = await rapidezFetch(config.magento_url + '/graphql', {
        method: 'POST',
        headers: {
            'Store': window.config.store_code,
            'Authorization': window.app.loggedIn ? `Bearer ${localStorage.token}` : null,
            'Content-Type': 'application/json',
            ...(options?.headers || {})
        },
        body: JSON.stringify({
            query: query,
            variables: variables,
        })
    })

    if (!response.ok) {
        throw new Error(window.config.translations.errors.wrong)
    }

    let data = await response.json()

    if (data.errors) {
        console.error(data.errors)

        if (data.errors[0]?.extensions?.category === 'graphql-authorization') {
            if (options?.notifyOnError || true) {
                Notify(window.config.translations.errors.session_expired)
            }

            if (options?.redirectOnExpiration || true) {
                this.logout(window.url('/login'))
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired)
            }
        }

        throw new Error(data.errors[0].message)
    }

    return data
}

window.magentoAPI = async (method, endpoint, data = {}, options = {
    headers: {},
    redirectOnExpiration: true,
    notifyOnError: true,
}) => {
    let response = await rapidezFetch(config.magento_url + '/rest/' + window.config.store_code + '/V1/' + endpoint, {
        method: method.toUpperCase(),
        headers: {
            'Authorization': window.app.loggedIn ? `Bearer ${localStorage.token}` : null,
            'Content-Type': 'application/json',
            ...(options?.headers || {})
        },
        body: JSON.stringify(data)
    })

    if (!response.ok) {
        if ([401, 404].includes(response.status)) {
            if (options?.notifyOnError || true) {
                Notify(window.config.translations.errors.session_expired)
            }

            if (options?.redirectOnExpiration || true) {
                this.logout(window.url('/login'))
            } else {
                throw new SessionExpired(window.config.translations.errors.session_expired)
            }
        }

        if (options?.notifyOnError || true) {
            Notify(window.config.translations.errors.wrong)
        }

        throw new Error(window.config.translations.errors.wrong)
    }

    let responseData = await response.json()

    return responseData
}

