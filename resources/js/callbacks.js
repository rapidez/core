import { cart, clear as clearCart, getAttributeValues } from './stores/useCart'
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

Vue.prototype.submitFieldsets = async function (form) {
    let promises = []
    form.querySelectorAll('[data-function]').forEach((fieldset) => {
        if (!fieldset?.dataset?.function || !fieldset?.__vue__) {
            return
        }

        promises.push(
            fieldset.__vue__[fieldset?.dataset?.function]().then((result) => {
                if (result === false) {
                    throw new Error()
                }
            }),
        )
    })

    return await Promise.all(promises)
}

Vue.prototype.updateCart = async function (variables, response) {
    cart.value = 'cart' in Object.values(response.data)[0] ? Object.values(response.data)[0].cart : Object.values(response.data)[0]

    getAttributeValues().then((response) => {
        if (!response?.data?.customAttributeMetadata?.items) {
            return
        }

        const mapping = Object.fromEntries(
            response.data.customAttributeMetadata.items.map((attribute) => [
                attribute.attribute_code,
                Object.fromEntries(attribute.attribute_options.map((value) => [value.value, value.label])),
            ]),
        )

        cart.value.items = cart.value.items.map((cartItem) => {
            cartItem.product.attribute_values = {}

            for (const key in mapping) {
                cartItem.product.attribute_values[key] = cartItem.product[key]
                if (cartItem.product.attribute_values[key] === null) {
                    continue
                }

                if (typeof cartItem.product.attribute_values[key] === 'string') {
                    cartItem.product.attribute_values[key] = cartItem.product.attribute_values[key].split(',')
                }

                if (typeof cartItem.product.attribute_values[key] !== 'object') {
                    cartItem.product.attribute_values[key] = [cartItem.product.attribute_values[key]]
                }

                cartItem.product.attribute_values[key] = cartItem.product.attribute_values[key].map((value) => mapping[key][value] || value)
            }

            return cartItem
        })
    })

    return response.data
}

Vue.prototype.checkResponseForExpiredCart = async function (variables, response) {
    if (
        response?.errors?.some(
            (error) =>
                error.extensions.category === 'graphql-no-such-entity' &&
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

Vue.prototype.checkResponseForExpiredCart = async function (variables, response) {
    if (
        response?.errors?.some(
            (error) =>
                error.extensions.category === 'graphql-no-such-entity' &&
                error.path.some((path) =>
                    [
                        'cart',
                        'customerCart',
                        'assignCustomerToGuestCart',
                        'mergeCarts',
                        'addProductsToCart',
                        'removeItemFromCart',
                        'updateCartItems',
                    ].includes(path),
                ),
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
