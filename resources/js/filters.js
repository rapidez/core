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
    return (window.config.base_url || window.origin) + path
}

Vue.filter('url', window.url)
