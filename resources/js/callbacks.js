import { cart, clear as clearCart, getAttributeValues } from './stores/useCart'
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

Vue.prototype.updateCart = async function (data, response) {
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

Vue.prototype.checkResponseForExpiredCart = async function (error) {
    let responseData = await error.response?.json()

    if (
        responseData?.errors?.some(
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
