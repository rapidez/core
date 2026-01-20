import { useEventListener } from '@vueuse/core'
import { cart, clear as clearCart } from './stores/useCart'
import { fillFromGraphqlResponse as updateOrder } from './stores/useOrder'
import { runAfterPlaceOrderHandlers, runBeforePaymentMethodHandlers, runBeforePlaceOrderHandlers } from './stores/usePaymentHandlers'
import { refresh as refreshUser, token } from './stores/useUser'

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue
    vue.config.globalProperties.scrollToElement = (selector, offset = 0) => {
        let el = window.document.querySelector(selector)
        if (el) {
            window.scrollTo({
                top: el.offsetTop - offset,
                behavior: 'smooth',
            })
        }
    }

    vue.config.globalProperties.getCheckoutStep = (stepName) => {
        return config.checkout_steps?.indexOf(stepName)
    }

    vue.config.globalProperties.submitPartials = async function (form, sequential = false) {
        let promises = []
        for (const element of form.querySelectorAll('[partial-submit]')) {
            let resolveFn, rejectFn
            const createdPromise = new Promise((res, rej) => {
                resolveFn = res
                rejectFn = rej
            }).then((result) => {
                if (result === false) {
                    throw new Error('Result was false')
                }
            })

            const e = new CustomEvent('partial-submit', {
                detail: { resolve: resolveFn, reject: rejectFn },
                bubbles: false,
                cancelable: true,
            })

            const dispatched = element.dispatchEvent(e)
            if (!dispatched) {
                resolveFn()
            }

            if (sequential) {
                await createdPromise
            }

            promises.push(createdPromise)
        }

        return await Promise.all(promises)
    }

    vue.config.globalProperties.checkResponseForExpiredCart = async function (variables, response) {
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

        await vue.config.globalProperties.updateCart(variables, response)

        return false
    }

    vue.config.globalProperties.updateCart = async function (data, response) {
        console.log('updateCart')
        if (!response?.data) {
            return response?.data
        }
        cart.value =
            Object.values(response.data)
                .map((queryResponse) => (queryResponse && 'cart' in queryResponse ? queryResponse.cart : queryResponse))
                .findLast((queryResponse) => queryResponse?.is_virtual !== undefined) ?? cart.value

        window.$emit('cart-updated', {
            cart: cart,
        })

        return response.data
    }

    vue.config.globalProperties.updateOrder = async function (data, response) {
        await updateOrder(data, response)

        return response.data
    }

    vue.config.globalProperties.handleBeforePaymentMethodHandlers = runBeforePaymentMethodHandlers
    vue.config.globalProperties.handleBeforePlaceOrderHandlers = runBeforePlaceOrderHandlers

    vue.config.globalProperties.handlePlaceOrder = async function (data, response) {
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
})
