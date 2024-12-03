import { cart, clear as clearCart } from './stores/useCart'
import { fillFromGraphqlResponse as updateOrder, order } from './stores/useOrder'
import { runAfterPlaceOrderHandlers, runBeforePaymentMethodHandlers, runBeforePlaceOrderHandlers } from './stores/usePaymentHandlers'
import { refresh as refreshUser, token } from './stores/useUser'

Vue.prototype.scrollToElement = (selector) => {
    let el = window.document.querySelector(selector)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}

Vue.prototype.getCheckoutStep = (stepName) => {
    return (config.checkout_steps[config.store_code] ?? config.checkout_steps['default'])?.indexOf(stepName)
}

Vue.prototype.submitPartials = async function (form) {
    let promises = []
    form.querySelectorAll('[partial-submit]').forEach((element) => {
        const partialFn = element?.getAttribute('partial-submit')
        if (!partialFn || !element?.__vue__) {
            return
        }

        promises.push(
            element.__vue__[partialFn]().then((result) => {
                if (result === false) {
                    throw new Error()
                }
            }),
        )
    })

    return await Promise.all(promises)
}

Vue.prototype.checkResponseForExpiredCart = async function (variables, response) {
    if (
        response?.errors?.some(
            (error) =>
                error.extensions?.category === 'graphql-no-such-entity' &&
                // Untested, but something like this is maybe a better idea as
                // we're using a lot of different mutations in the checkout.
                error.path.some((path) => path.toLowerCase().includes('cart')),
        )
    ) {
        Notify(window.config.translations.errors.cart_expired, 'error')
        clearCart()
        if (token.value !== undefined) {
            // If the cart has expired, check if the session is not expired
            refreshUser()
        }

        return true
    }

    return false
}

Vue.prototype.updateCart = async function (data, response) {
    if (!response?.data) {
        return response?.data
    }
    cart.value = 'cart' in Object.values(response.data)[0] ? Object.values(response.data)[0].cart : Object.values(response.data)[0]

    return response.data
}

Vue.prototype.updateOrder = async function (data, response) {
    await updateOrder(data, response)

    return response.data
}

Vue.prototype.handleBeforePaymentMethodHandlers = runBeforePaymentMethodHandlers
Vue.prototype.handleBeforePlaceOrderHandlers = runBeforePlaceOrderHandlers

Vue.prototype.handlePlaceOrder = async function (data, response) {
    if (!response?.data) {
        return response?.data
    }

    if (!response?.data?.placeOrder?.orderV2 && response?.data?.placeOrder?.errors) {
        const message = response.data.placeOrder.errors.find(() => true).message
        Notify(message, 'error')
        throw new Error(message)
    }

    await updateOrder(data, response)
    await runAfterPlaceOrderHandlers(response, this)

    return response.data
}
