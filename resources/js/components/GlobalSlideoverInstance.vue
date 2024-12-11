<script>
export default {
    render() {
        return this.$scopedSlots.default(this)
    },
    data() {
        return {
            content: '',
            title: '',
            position: 'left',
        }
    },
    mounted() {
        this.$root.$on('global-slideover-open', (data) => {
            this.content = data.content || ''
            this.title = data.title || ''
            this.position = data.position || 'left'
            this.$el.querySelector('#slideover-global').checked = true
        })
    },
}
</script>
