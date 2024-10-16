let beforePaymentMethodHandlers = []

// Pushing your handler here will schedule it to receive the query, variables and options for setPaymentMethodOnCart.
// it must return an array with the same structure.
export async function addBeforePaymentMethodHandler(promise) {
    beforePaymentMethodHandlers.push(promise)
}

export async function runBeforePaymentMethodHandlers(query, variables, options) {
    if (!beforePaymentMethodHandlers.length) {
        return [query, variables, options]
    }
    let results = await Promise.all(beforePaymentMethodHandlers.map((handler) => handler(query, variables, options)))
    return results.reduce(
        (old, current) => (JSON.stringify(current) === JSON.stringify([query, variables, options]) ? old : current),
        [query, variables, options],
    )
}

let beforePlaceOrderHandlers = []

// Pushing your handler here will schedule it to receive the query, variables and options for PlaceOrder.
// it must return an array with the same structure.
export async function addBeforePlaceOrderHandler(promise) {
    beforePlaceOrderHandlers.push(promise)
}

export async function runBeforePlaceOrderHandlers(query, variables, options) {
    if (!beforePlaceOrderHandlers.length) {
        return [query, variables, options]
    }
    let results = await Promise.all(beforePlaceOrderHandlers.map((handler) => handler(query, variables, options)))
    return results.reduce(
        (old, current) => (JSON.stringify(current) === JSON.stringify([query, variables, options]) ? old : current),
        [query, variables, options],
    )
}

let afterPlaceOrderHandlers = []

// Pushing your handler here will schedule it to receive the order and instance of the mutationComponent
// If you wish to change the redirect, set `mutationComponent.redirect = '<path to new url>'`
export async function addAfterPlaceOrderHandler(promise) {
    afterPlaceOrderHandlers.push(promise)
}

export async function runAfterPlaceOrderHandlers(response, mutationComponent) {
    return await Promise.all(afterPlaceOrderHandlers.map((handler) => handler(response, mutationComponent)))
}
