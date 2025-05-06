export let instantsearchMiddlewares = []

export async function addInstantsearchMiddleware(middlewareFn) {
    instantsearchMiddlewares.push(middlewareFn)
}

export async function removeInstantsearchMiddleware(middlewareFn) {
    instantsearchMiddlewares = instantsearchMiddlewares.filter((registeredMiddleware) => registeredMiddleware !== middlewareFn)
}
