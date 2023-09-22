<script>
import InteractWithUser from './mixins/InteractWithUser'

export default {
    mixins: [InteractWithUser],

    render() {
        return this.$scopedSlots.default(this)
    },
}
</script>
