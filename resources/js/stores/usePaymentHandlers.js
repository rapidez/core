
let afterPlaceOrdersHandlers = []

// Pushing your handler here will schedule it to receive the order and instance of the mutationComponent
// If you wish to change the redirect, set `mutationComponent.redirect = '<path to new url>'`
export async function addAfterPlaceOrderHandler(promise) {
    afterPlaceOrdersHandlers.push(promise);
}

export async function runAfterPlaceOrderHandlers(order, mutationComponent) {
    return await Promise.all(afterPlaceOrdersHandlers.map(handler => handler(order, mutationComponent)))
}
