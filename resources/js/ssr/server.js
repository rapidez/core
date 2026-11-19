// POC: render the listing server side with Vue's server renderer.
// Laravel posts the Blade output of the listing (the Vue template), the
// url and the frontend config; this returns the rendered HTML and the
// search results so the browser can start without a search request.
import { setUrl } from './dom.js'

window.debug = false
window.Notify = () => {}
window.process ??= {}

import '../polyfills'
import '../vue'
import '../fetch'
import '../helpers'
import '../mixins'
import '../cookies'
import '../callbacks'
import '../vue-components'
import '../instantsearch'
import { cart } from '../stores/useCart'
import { user } from '../stores/useUser'
import useMask from '../stores/useMask'
import useOrder from '../stores/useOrder'
import { fetchCount } from '../stores/useFetches'

import http from 'node:http'
import { computed, createSSRApp, ref } from 'vue'
import { renderToString } from 'vue/server-renderer'
import { createServerRootMixin } from 'vue-instantsearch/vue3/es/src/util/createServerRootMixin.js'
import InstantSearch from 'vue-instantsearch/vue3/es/src/components/InstantSearch.js'
import { waitForResults, getInitialResults } from 'instantsearch.js/es/lib/server.js'

const port = process.env.RAPIDEZ_SSR_PORT || 13714

// Replaces <ais-instant-search> during SSR. On the first pass it creates an
// instance and starts it so the widgets register and a search is done. On
// the second pass it reuses that, by then hydrated, instance so the widgets
// render with the results.
const { data: _data, created: _created, mounted: _mounted, ...instantSearchBase } = InstantSearch
const SsrInstantSearch = {
    ...instantSearchBase,
    inject: { ssr: { from: '$_rapidez_ssr' } },
    data() {
        const index = this.ssr.counter++
        let instance = this.ssr.instances[index]

        if (!instance) {
            instance = createServerRootMixin({
                searchClient: this.searchClient,
                indexName: this.indexName,
                routing: this.routing,
                initialUiState: this.initialUiState,
                future: this.future,
            }).data().instantsearch
            this.ssr.instances[index] = instance
        }

        return { instantSearchInstance: instance }
    },
    created() {
        if (this.ssr.pass === 1) {
            this.instantSearchInstance.start()
            this.instantSearchInstance.started = false
        }
    },
}

function createApp(template, ssr) {
    // Same root methods as the browser root in package.js.
    const app = createSSRApp({
        template: `<div>${template}</div>`,
        methods: {
            categoryPositions(categoryId) {
                return {
                    function_score: {
                        script_score: {
                            script: {
                                source: `doc.containsKey('positions.${categoryId}') && !doc['positions.${categoryId}'].empty && doc['positions.${categoryId}'].value =~ /^\\d+$/ ? Integer.parseInt(doc['positions.${categoryId}'].value) : 0`,
                            },
                        },
                    },
                }
            },
        },
    })

    window.app = app
    app.config.globalProperties = {
        ...app.config.globalProperties,
        custom: ref({}),
        config: window.config,
        refs: ref({}),
        loadingCount: fetchCount,
        loading: ref(false),
        cart: cart,
        order: useOrder(),
        user: user,
        mask: useMask(),
        showTax: window.config.show_tax,
        configError: ref(false),
        loggedIn: computed(() => false),
        hasCart: computed(() => false),
        window: window,
    }

    document.dispatchEvent(new CustomEvent('vue:loaded', { detail: { vue: app } }))

    app._context.components['ais-instant-search'] = SsrInstantSearch
    app.provide('$_rapidez_ssr', ssr)
    app.config.warnHandler = (msg) => ssr.warnings.push(msg)

    return app
}

async function render({ url, template, config }) {
    const start = performance.now()
    setUrl(url)
    window.config = config

    // The custom v-txt / v-htm directives (vue.js) set the content when mounted,
    // which doesn't happen server side. The native ones do work with SSR.
    template = template.replace(/\bv-txt=/g, 'v-text=').replace(/\bv-htm=/g, 'v-html=')

    const ssr = { pass: 1, counter: 0, instances: [], warnings: [] }

    await renderToString(createApp(template, ssr))
    const firstPass = performance.now()

    const results = await Promise.all(
        ssr.instances.map(async (instance) => {
            const requestParams = await waitForResults(instance)
            const initialResults = getInitialResults(instance.mainIndex, requestParams)
            instance.hydrate(initialResults)

            return initialResults
        }),
    )
    const search = performance.now()

    ssr.pass = 2
    ssr.counter = 0
    const html = await renderToString(createApp(template, ssr))
    const secondPass = performance.now()

    ssr.instances.forEach((instance) => instance.dispose?.())

    return {
        html,
        results,
        warnings: [...new Set(ssr.warnings)],
        timings: {
            first_pass: Math.round(firstPass - start),
            search: Math.round(search - firstPass),
            second_pass: Math.round(secondPass - search),
            total: Math.round(secondPass - start),
        },
    }
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
            const result = await enqueue(() => render(JSON.parse(body)))
            response.writeHead(200, { 'Content-Type': 'application/json' })
            response.end(JSON.stringify(result))
        } catch (error) {
            console.error(error)
            response.writeHead(500, { 'Content-Type': 'application/json' })
            response.end(JSON.stringify({ error: error.message, stack: error.stack }))
        }
    })
}).listen(port, () => console.log(`Rapidez SSR listening on ${port}`))
