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
        window.$on('global-slideover-open', async (data) => {
            this.title = data.title || ''
            this.position = data.position || 'left'
            await this.$nextTick(() => document.getElementById('slideover-global')?.showModal())
        })
    },
}
</script>
