export let instantsearchMiddlewares = []

export async function addInstansearchMiddleware(middlewareFn) {
    instantsearchMiddlewares.push(middlewareFn)
}

export async function removeInstansearchMiddleware(middlewareFn) {
    instantsearchMiddlewares = instantsearchMiddlewares.filter((registeredMiddleware) => registeredMiddleware !== middlewareFn)
}
