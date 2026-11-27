// POC: render the listing server side with Vue's server renderer.
// Laravel posts the Blade output of the listing (the Vue template), the
// url and the frontend config; this returns the rendered HTML and the
// search results so the browser can start without a search request.
import './dom.js'
import http from 'node:http'

const port = process.env.RAPIDEZ_SSR_PORT || 13714

let booting = null
function boot(config) {
    booting ??= (async () => {
        window.config = config
        await import('rapidez-ssr-app')

        return (await import('./render.js')).render
    })().catch((error) => {
        booting = null
        throw error
    })

    return booting
}

// A component throwing async shouldn't take the server down.
process.on('uncaughtException', (error) => console.error('Uncaught', error))
process.on('unhandledRejection', (error) => console.error('Unhandled', error))

// The fake browser (url, config) is global, so render one at a time.
let queue = Promise.resolve()
const enqueue = (job) => (queue = queue.then(job, job))

http.createServer((request, response) => {
    if (request.method === 'GET' && request.url === '/health') {
        response.end('ok')
        return
    }

    let body = ''
    request.on('data', (chunk) => (body += chunk))
    request.on('end', async () => {
        try {
            const payload = JSON.parse(body)
            const render = await boot(payload.config)
            const result = await enqueue(() => render(payload))
            response.writeHead(200, { 'Content-Type': 'application/json' })
            response.end(JSON.stringify(result))
        } catch (error) {
            console.error(error)
            response.writeHead(500, { 'Content-Type': 'application/json' })
            response.end(JSON.stringify({ error: error.message, stack: error.stack }))
        }
    })
}).listen(port, () => console.log(`Rapidez SSR listening on ${port}`))
