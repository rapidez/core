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
        window.$on('global-slideover-open', (data) => {
            this.title = data.title || ''
            this.position = data.position || 'left'
            this.$el.nextSibling.querySelector('#slideover-global').checked = true
        })
    },
}
</script>
