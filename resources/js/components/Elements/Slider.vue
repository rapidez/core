<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                navigate: this.navigate,
                showLeft: this.showLeft,
                showRight: this.showRight,
            })
        },
        data: () => {
            return {
                position: 0,
                showLeft: false,
                showRight: false,
            }
        },
        mounted() {
            this.slider.addEventListener('scroll', this.scroll)
            this.slider.dispatchEvent(new CustomEvent('scroll'))
        },
        beforeDestroy() {
            this.slider.removeEventListener('scroll', this.scroll)
        },
        methods: {
            scroll(event) {
                this.position = event.currentTarget.scrollLeft

                this.showLeft = this.position
                this.showRight = this.slider.offsetWidth + this.position != this.slider.scrollWidth
            },

            navigate(direction) {
                this.slider.scrollTo(this.position + this.slider.childNodes[0]?.offsetWidth * direction, 0)
            }
        },
        computed: {
            slider() {
                return this.$scopedSlots.default()[0].context.$refs.slider
            },
        }
    }
</script>
