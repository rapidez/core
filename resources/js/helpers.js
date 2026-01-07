import { user } from './stores/useUser'

window.truncate = function (value, limit) {
    if (value.length > limit) {
        value = value.substring(0, limit - 3) + '...'
    }

    return value
}

window.price = function (value, extra = {}) {
    return new Intl.NumberFormat(config.locale.replace('_', '-'), {
        style: 'currency',
        currency: config.currency,
        ...extra,
    }).format(value)
}

window.productPrice = function (product) {
    return product.price
}

window.productSpecialPrice = function (product) {
    let groupId = user?.value?.group_id
    let specialPrice = groupId ? product.prices[groupId].min_price : product.special_price

    return window.productPrice(product) > specialPrice ? specialPrice : null
}

window.sumPrices = function (price1, price2) {
    return Math.round((parseFloat(price1) + parseFloat(price2)) * 100) / 100
}

window.url = function (path = '') {
    // Transform urls starting with / into url with domain
    if (!path.startsWith('/')) {
        return path
    }

    return (window.config.base_url || window.origin) + path
}

window.stripHtmlTags = function (html, safeTags = ['mark']) {
    safeTags = safeTags.map((tag) => tag.replace(/[^a-zA-Z0-9-]/g, '')).filter(Boolean)
    return html.replace(new RegExp('<(?!/?(?:' + safeTags.join('|') + ')>)(?:.|\n)*?>', 'gm'), '') // Safe tags are only allowed if they have NO attributes
}

window.htmlDecode = function (input) {
    return new DOMParser().parseFromString(input, 'text/html')?.documentElement?.textContent ?? input
}

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue
    vue.config.globalProperties.truncate = window.truncate
    vue.config.globalProperties.price = window.price
    vue.config.globalProperties.productPrice = window.productPrice
    vue.config.globalProperties.productSpecialPrice = window.productSpecialPrice
    vue.config.globalProperties.sumPrices = window.sumPrices
    vue.config.globalProperties.url = window.url
    vue.config.globalProperties.stripHtmlTags = window.stripHtmlTags
    vue.config.globalProperties.htmlDecode = window.htmlDecode
})
