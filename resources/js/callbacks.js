import { useLocalStorage, StorageSerializers } from '@vueuse/core'

Vue.prototype.scrollToElement = (selector) => {
    let el = window.document.querySelector(selector)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}

Vue.prototype.refreshCart = async function (data, response) {
    window.app.cart = 'cart' in Object.values(response.data.data)[0]
        ? Object.values(response.data.data)[0].cart
        : Object.values(response.data.data)[0]

    return response.data.data
}
