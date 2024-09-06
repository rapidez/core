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
    // Replace any double slashes not preceded by a : with a single slash
    path = path.replaceAll(/([^:])\/\//g, '$1/')
    
    // Transform urls starting with / into url with domain
    if (!path.startsWith('/')) {
        return path
    }

    return (window.config.base_url || window.origin) + path
}

Vue.filter('url', window.url)
