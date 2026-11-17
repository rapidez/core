<script>
import ClearRefinements from 'vue-instantsearch/vue3/es/src/components/ClearRefinements.vue.js'

export default {
    name: 'AisClearRefinements',
    extends: ClearRefinements,
    props: {
        excludedAttributes: {
            type: Array,
            default: () => [...config.category_attributes, 'query'],
        },
    },
}
</script>
