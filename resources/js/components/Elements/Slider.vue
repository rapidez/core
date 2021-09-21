<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                navigate: this.navigate,
                showLeft: this.showLeft,
                showRight: this.showRight,
                currentSlide: this.currentSlide
            })
        },
        data: () => {
            return {
                position: 0,
                showLeft: false,
                showRight: false,
                mounted: false
            }
        },
        mounted() {
            this.slider.addEventListener('scroll', this.scroll)
            this.slider.dispatchEvent(new CustomEvent('scroll'))
            this.mounted = true
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

            navigate(index) {
                this.slider.scrollTo(this.slider.childNodes[0]?.offsetWidth * index, 0)
            }
        },
        computed: {
            slider() {
                return this.$scopedSlots.default()[0].context.$refs.slider
            },
            currentSlide() {
                if (this.mounted) {
                    return Math.round(this.position / this.slider.childNodes[0]?.offsetWidth)
                }
            }
        }
    }
</script>
