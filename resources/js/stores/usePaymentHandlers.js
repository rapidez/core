
let beforePaymentMethodHandlers = []

// Pushing your handler here will schedule it to receive the query, variables and options for add to cart.
// it must return an array with the same structure.
export async function addBeforePaymentMethodHandler(promise) {
    beforePaymentMethodHandlers.push(promise);
}

export async function runBeforePaymentMethodHandlers(query, variables, options) {
    if (!beforePaymentMethodHandlers.length) {
        return [query, variables, options];
    }
    let results = await Promise.all(beforePaymentMethodHandlers.map(handler => handler(query, variables, options)))
    return results.reduce((old, current) => JSON.stringify(current) === JSON.stringify(variables) ? old : current, variables)
}

let afterPlaceOrdersHandlers = []

// Pushing your handler here will schedule it to receive the order and instance of the mutationComponent
// If you wish to change the redirect, set `mutationComponent.redirect = '<path to new url>'`
export async function addAfterPlaceOrderHandler(promise) {
    afterPlaceOrdersHandlers.push(promise);
}

export async function runAfterPlaceOrderHandlers(order, mutationComponent) {
    return await Promise.all(afterPlaceOrdersHandlers.map(handler => handler(order, mutationComponent)))
}
