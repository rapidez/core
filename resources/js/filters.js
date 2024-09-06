Vue.filter('truncate', function (value, limit) {
    if (value.length > limit) {
        value = value.substring(0, limit - 3) + '...'
    }

    return value
})

Vue.filter('price', function (value) {
    return new Intl.NumberFormat(config.locale.replace('_', '-'), {
        style: 'currency',
        currency: config.currency,
    }).format(value)
})

window.url = function (path = '') {
    // Transform urls starting with / into url with domain
    if (path.startsWith('/')) {
        path = (window.config.base_url || window.origin) + path
    }

    // Replace any double slashes not preceded by a : with a single slash
    return path.replaceAll(/([^:])\/\//g, '$1/')
}

Vue.filter('url', window.url)
