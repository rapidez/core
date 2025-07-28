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
        document.addEventListener('rapidez:global-slideover-open', (event) => {
            const data = event.detail
            this.title = data.title || ''
            this.position = data.position || 'left'
            this.$el.querySelector('#slideover-global').checked = true
        })
    },
}
</script>
