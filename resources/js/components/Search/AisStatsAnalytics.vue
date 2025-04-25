<script>
import { connectStats } from 'instantsearch.js/es/connectors';
import { createSuitMixin } from 'vue-instantsearch/vue2/es/src/mixins/suit';
import { createWidgetMixin } from 'vue-instantsearch/vue2/es/src/mixins/widget';

export default {
    name: 'AisStatsAnalytics',
    mixins: [
        createWidgetMixin(
            { connector: connectStats },
            {
                $$widgetType: 'ais.stats-analytics',
            }
        ),
        createSuitMixin({ name: 'Stats-Analytics' }),
    ],
    watch: {
        'state.query': {
            handler(query) {
                this.instantSearchInstance.sendEventToInsights({
                    insightsMethod: 'viewedObjectIDs',
                    eventType: 'search',
                    eventModifier: 'external',
                    widgetType: 'ais.stats-analytics',
                    payload: {
                        eventName: 'Query',
                        query: query,
                        nbHits: this.state.nbHits,
                        processingTimeMS: this.state.processingTimeMS,
                    },
                    instantSearchInstance: this.instantSearchInstance,
                });
            },
        },
    },
    computed: {
        widgetParams() {
            return {};
        },
    },
};
</script>
<template>
</template>
