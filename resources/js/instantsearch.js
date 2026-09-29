import { defineAsyncComponent } from 'vue'
import { addQuery } from './stores/useSearchHistory'

const instantsearchComponents = import('./instantsearch-components')
const component = (name) => defineAsyncComponent(() => instantsearchComponents.then((m) => m[name]))

document.addEventListener('vue:loaded', function (event) {
    const vue = event.detail.vue

    // Shared between Autocomplete and Listing
    vue.component('ais-instant-search', component('InstantSearch'))
    vue.component('ais-hits', component('Hits'))
    vue.component('ais-infinite-hits', component('InfiniteHits'))
    vue.component('ais-configure', component('Configure'))
    vue.component('ais-autocomplete', component('Autocomplete'))
    vue.component('ais-search-box', component('SearchBox'))
    vue.component('ais-state-results', component('StateResults'))

    // Used by Autocomplete
    vue.component('ais-index', component('Index'))

    // // Used by Listing
    vue.component('ais-refinement-list', component('RefinementList'))
    vue.component('ais-hierarchical-menu', component('HierarchicalMenu'))
    vue.component('ais-range-input', component('RangeInput'))
    vue.component('ais-current-refinements', component('CurrentRefinements'))
    vue.component('ais-clear-refinements', component('ClearRefinements'))
    vue.component('ais-hits-per-page', component('HitsPerPage'))
    vue.component('ais-sort-by', component('SortBy'))
    vue.component('ais-pagination', component('Pagination'))
    vue.component('ais-stats', component('Stats'))

    // Custom component
    vue.component('ais-stats-analytics', component('StatsAnalytics'))
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
