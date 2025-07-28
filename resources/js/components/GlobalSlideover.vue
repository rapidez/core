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
        document.addEventListener('rapidez:global-slideover-open', (event) => {
            this.isCurrentSlideover = event.detail.initiator === this
        })
    },
    methods: {
        open() {
            document.dispatchEvent(new CustomEvent('rapidez:global-slideover-open', { detail: { title: this.title, position: this.position, initiator: this } }));
        },
    },
}
</script>
