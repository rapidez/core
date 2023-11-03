import { useLocalStorage, StorageSerializers } from '@vueuse/core'

Vue.prototype.scrollToElement = (selector) => {
    let el = window.document.querySelector(selector)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}

Vue.prototype.refreshCart = async function (data, response) {
    useLocalStorage('graphql_cart', null, { serializer: StorageSerializers.object }).value = Object.values(response.data.data)[0]
    window.app.$emit('cart-refreshed')
    // TODO: This refresh is to late and uses the old data? Should we somehow
    // async "wait" for it? Or should we use a reactive global store like
    // before with "window.app.cart"?
}
