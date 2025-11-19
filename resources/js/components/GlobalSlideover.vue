<script>
export default {
    props: {
        title: String,
        position: String,
    },
    data() {
        return {
            isCurrentSlideover: false,
        }
    },
    render() {
        return this.$slots.default(this)
    },
    mounted() {
        window.$on('global-slideover-open', (data) => {
            this.isCurrentSlideover = data.initiator === this
        })
    },
    methods: {
        open() {
            window.$emit('global-slideover-open', { title: this.title, position: this.position, initiator: this })
        },
    },
}
</script>
