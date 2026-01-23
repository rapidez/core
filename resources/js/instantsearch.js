import { defineAsyncComponent } from 'vue'
import { addQuery } from './stores/useSearchHistory'

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue
    // Shared between Autocomplete and Listing
    vue.component(
        'ais-instant-search',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/InstantSearch')),
    )
    vue.component(
        'ais-hits',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Hits.js')),
    )
    vue.component(
        'ais-infinite-hits',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/InfiniteHits.vue.js')),
    )
    vue.component(
        'ais-configure',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Configure.js')),
    )
    vue.component(
        'ais-autocomplete',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Autocomplete.vue.js')),
    )
    vue.component(
        'ais-search-box',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/SearchBox.vue.js')),
    )
    vue.component(
        'ais-state-results',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/StateResults.vue.js')),
    )

    // Used by Autocomplete
    vue.component(
        'ais-index',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Index.js')),
    )

    // Used by Listing
    vue.component(
        'ais-refinement-list',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/RefinementList.vue.js')),
    )
    vue.component(
        'ais-hierarchical-menu',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/HierarchicalMenu.vue.js')),
    )
    vue.component(
        'ais-range-input',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/RangeInput.vue.js')),
    )
    vue.component(
        'ais-current-refinements',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/CurrentRefinements.vue.js')),
    )
    vue.component(
        'ais-clear-refinements',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/ClearRefinements.vue.js')),
    )
    vue.component(
        'ais-hits-per-page',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/HitsPerPage.vue.js')),
    )
    vue.component(
        'ais-sort-by',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/SortBy.vue.js')),
    )
    vue.component(
        'ais-pagination',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Pagination.vue.js')),
    )
    vue.component(
        'ais-stats',
        defineAsyncComponent(() => import('vue-instantsearch/vue3/es/src/components/Stats.vue.js')),
    )
    vue.component(
        'ais-stats-analytics',
        defineAsyncComponent(() => import('./components/Search/AisStatsAnalytics.vue')),
    )
})

document.addEventListener('insights-event:viewedObjectIDs', (event) => {
    setTimeout(() => {
        if (event?.detail?.insightsEvent?.eventType !== 'search') {
            return
        }

        let url = new URL(window.location.href)
        if (
            url.pathname !== '/search' ||
            !url.searchParams.has('q') ||
            url.searchParams.get('q') !== event.detail.insightsEvent.payload.query
        ) {
            // Do not track autocomplete
            return
        }

        if (event.detail.insightsEvent.payload.nbHits < 1) {
            return
        }

        addQuery(event.detail.insightsEvent.payload.query, { hits: event.detail.insightsEvent.payload.nbHits })
    })
})
