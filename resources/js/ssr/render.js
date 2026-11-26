import { setUrl } from './dom.js'
import { createRapidezApp } from '../root'
import { createSSRApp } from 'vue'
import { renderToString } from 'vue/server-renderer'
import { createServerRootMixin } from 'vue-instantsearch/vue3/es/src/util/createServerRootMixin.js'
import InstantSearch from 'vue-instantsearch/vue3/es/src/components/InstantSearch.js'
import { waitForResults, getInitialResults } from 'instantsearch.js/es/lib/server.js'

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
    const app = createRapidezApp(createSSRApp, { template: `<div>${template}</div>` })

    document.dispatchEvent(new CustomEvent('vue:loaded', { detail: { vue: app } }))

    app._context.components['ais-instant-search'] = SsrInstantSearch
    app.provide('$_rapidez_ssr', ssr)
    app.config.warnHandler = (msg) => ssr.warnings.push(msg)

    return app
}

export async function render({ url, template, config }) {
    const start = performance.now()
    setUrl(url)
    window.config = config

    // The custom v-txt / v-htm directives (vue.js) set the content when mounted,
    // which doesn't happen server side. The native ones do work with SSR.
    template = template.replace(/\bv-txt=/g, 'v-text=').replace(/\bv-htm=/g, 'v-html=')

    const ssr = { pass: 1, counter: 0, instances: [], warnings: [] }

    const app = createApp(template, ssr)

    await renderToString(app)
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
    const html = await renderToString(app)
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
