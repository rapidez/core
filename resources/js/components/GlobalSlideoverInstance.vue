<script>
export default {
    render() {
        return this.$slots.default(this)
    },
    data() {
        return {
            title: '',
            position: 'left',
        }
    },
    mounted() {
        window.$on('rapidez:global-slideover-open', (data) => {
            this.title = data.title || ''
            this.position = data.position || 'left'
            this.$el.querySelector('#slideover-global').checked = true
        })
    },
}
</script>
