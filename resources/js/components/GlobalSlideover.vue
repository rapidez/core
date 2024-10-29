<script>
export default {
    props: {
        title: String,
        position: String,
        contents: String,
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        open() {
            this.$root.globalSlideover.content = this.slideoverContents
            this.$root.globalSlideover.title = this.title
            this.$root.globalSlideover.position = this.position
        },
    },

    computed: {
        slideoverContents() {
            if (this.contents) {
                return this.contents
            }

            return this.$el.querySelector('.global-slideover-wrapper').innerHTML;
        }
    },
}
</script>
