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
        return this.$scopedSlots.default(this)
    },
    mounted() {
        this.$root.$on('global-slideover-open', (data) => {
            this.isCurrentSlideover = data.initiator === this
        })
    },
    methods: {
        open() {
            this.$root.$emit('global-slideover-open', { title: this.title, position: this.position, initiator: this })
        },
    },
}
</script>
