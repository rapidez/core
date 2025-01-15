<script>
export default {
    render() {
        return this.$scopedSlots.default(this)
    },
    data() {
        return {
            title: '',
            position: 'left',
        }
    },
    mounted() {
        this.$root.$on('global-slideover-open', (data) => {
            this.title = data.title || ''
            this.position = data.position || 'left'
            this.$el.querySelector('#slideover-global').checked = true
        })
    },
}
</script>
