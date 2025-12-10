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

window.url = function (path = '') {
    // Transform urls starting with / into url with domain
    if (!path.startsWith('/')) {
        return path
    }

    return (window.config.base_url || window.origin) + path
}

window.stripHtmlTags = function (html, safeTags = ['mark']) {
    safeTags = safeTags.map((tag) => tag.replace(/[^a-zA-Z0-9-]/g, '')).filter(Boolean);
    return html.replace(new RegExp('<(?!/?(?:' + safeTags.join('|') + ')>)(?:.|\n)*?>', 'gm'), '') // Safe tags are only allowed if they have NO attributes
}

window.htmlDecode = function (input) {
  return new DOMParser().parseFromString(input, "text/html")?.documentElement?.textContent ?? input;
}
