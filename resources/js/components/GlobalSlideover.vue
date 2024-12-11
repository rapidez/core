<script>
export default {
    props: {
        title: String,
        position: String,
        content: String,
    },
    render() {
        return this.$scopedSlots.default(this)
    },
    methods: {
        open() {
            this.$root.$emit('global-slideover-open', { content: this.content, title: this.title, position: this.position })
        },
    },
}
</script>
