<script>
    export default {
        render() {
            return this.$scopedSlots.default({
                navigate: this.navigate,
                showLeft: this.showLeft,
                showRight: this.showRight,
                currentSlide: this.currentSlide,
                slidesTotal: this.slidesTotal
            })
        },
        props: {
            vertical: {
                type: Boolean,
                default: false
            }
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
                this.position  = this.vertical ? event.currentTarget.scrollTop : event.currentTarget.scrollLeft
                this.showLeft = this.position
                this.showRight = (this.slider.offsetWidth + this.position) < this.slider.scrollWidth - 1
            },

            navigate(index) {
                this.vertical
                    ? this.slider.scrollTo(0, this.slider.children[index]?.offsetTop)
                    : this.slider.scrollTo(this.slider.children[0]?.offsetWidth * index, 0)
            }
        },
        computed: {
            slider() {
                return this.$element;
            },
            currentSlide() {
                if (this.mounted) {
                    return this.vertical
                        ? Math.round(this.position / this.slider.children[0]?.offsetHeight)
                        : Math.round(this.position / this.slider.children[0]?.offsetWidth)
                }
            },
            slidesTotal() {
                if (this.mounted) {
                    return this.vertical
                        ? Math.round(this.slider.scrollHeight / this.slider.offsetHeight)
                        : Math.round(this.slider.scrollWidth / this.slider.offsetWidth)
                }
            }
        }
    }
</script>
