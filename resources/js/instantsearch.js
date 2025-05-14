import { addQuery } from './stores/useSearchHistory'

// Shared between Autocomplete and Listing
Vue.component('ais-instant-search', () => import('vue-instantsearch/vue2/es/src/components/InstantSearch'))
Vue.component('ais-hits', () => import('vue-instantsearch/vue2/es/src/components/Hits.js'))
Vue.component('ais-configure', () => import('vue-instantsearch/vue2/es/src/components/Configure.js'))
Vue.component('ais-highlight', () => import('vue-instantsearch/vue2/es/src/components/Highlight.vue.js'))
Vue.component('ais-search-box', () => import('vue-instantsearch/vue2/es/src/components/SearchBox.vue.js'))
Vue.component('ais-state-results', () => import('vue-instantsearch/vue2/es/src/components/StateResults.vue.js'))

// Used by Autocomplete
Vue.component('ais-index', () => import('vue-instantsearch/vue2/es/src/components/Index.js'))

// Used by Listing
Vue.component('ais-refinement-list', () => import('vue-instantsearch/vue2/es/src/components/RefinementList.vue.js'))
Vue.component('ais-hierarchical-menu', () => import('vue-instantsearch/vue2/es/src/components/HierarchicalMenu.vue.js'))
Vue.component('ais-range-input', () => import('vue-instantsearch/vue2/es/src/components/RangeInput.vue.js'))
Vue.component('ais-current-refinements', () => import('vue-instantsearch/vue2/es/src/components/CurrentRefinements.vue.js'))
Vue.component('ais-clear-refinements', () => import('vue-instantsearch/vue2/es/src/components/ClearRefinements.vue.js'))
Vue.component('ais-hits-per-page', () => import('vue-instantsearch/vue2/es/src/components/HitsPerPage.vue.js'))
Vue.component('ais-sort-by', () => import('vue-instantsearch/vue2/es/src/components/SortBy.vue.js'))
Vue.component('ais-pagination', () => import('vue-instantsearch/vue2/es/src/components/Pagination.vue.js'))
Vue.component('ais-stats', () => import('vue-instantsearch/vue2/es/src/components/Stats.vue.js'))
Vue.component('ais-stats-analytics', () => import('./components/Search/AisStatsAnalytics.vue'))

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
