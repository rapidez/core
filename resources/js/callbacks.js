import { cart, clear as clearCart} from './stores/useCart'
import { mask } from './stores/useMask'
import { refresh as refreshUser, token} from './stores/useUser'

Vue.prototype.scrollToElement = (selector) => {
    let el = window.document.querySelector(selector)
    window.scrollTo({
        top: el.offsetTop,
        behavior: 'smooth',
    })
}

Vue.prototype.updateCart = async function (data, response) {
    cart.value = 'cart' in Object.values(response.data)[0]
        ? Object.values(response.data)[0].cart
        : Object.values(response.data)[0]

    return response.data
}

Vue.prototype.checkResponseForExpiredCart = async function (error) {
    let responseData = await error.response.json()

    if (
        responseData.errors?.some(error =>
            error.extensions.category === 'graphql-no-such-entity' &&
            error.path.some(path => ['cart', 'customerCart', 'assignCustomerToGuestCart', 'mergeCarts', 'addProductsToCart', 'removeItemFromCart', 'updateCartItems'].includes(path))
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

     return false;
}
