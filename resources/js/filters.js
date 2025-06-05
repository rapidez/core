window.truncate = function (value, limit) {
    if (value.length > limit) {
        value = value.substring(0, limit - 3) + '...'
    }

    return value
}

Vue.filter('truncate', window.truncate)

window.price = function (value, extra = {}) {
    return new Intl.NumberFormat(config.locale.replace('_', '-'), {
        style: 'currency',
        currency: config.currency,
        ...extra,
    }).format(value)
}

Vue.filter('price', window.price)

window.url = function (path = '') {
    // Transform urls starting with / into url with domain
    if (!path.startsWith('/')) {
        return path
    }

    return (window.config.base_url || window.origin) + path
}

Vue.filter('url', window.url)
