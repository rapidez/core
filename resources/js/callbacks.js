Vue.prototype.scrollToElement = (selector) => {
    let el = window.document.querySelector(selector)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}

Vue.prototype.getCheckoutStep = (stepName) => {
    return (config.checkout_steps[config.store_code] ?? config.checkout_steps['default'])?.indexOf(stepName)?.indexOf(stepName)
}
