<script>
    import {
        ReactiveBase,
        DataSearch,
    } from '@appbaseio/reactivesearch-vue'

    Vue.use(ReactiveBase)
    Vue.use(DataSearch)

    export default {
        render() {
            return this.$scopedSlots.default()
        },
    }
</script>
