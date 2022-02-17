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
            reference: {
                type: String,
                default: 'slider'
            },
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
            document.onreadystatechange = () => {
                if (document.readyState == "complete") {
                    this.slider.dispatchEvent(new CustomEvent('scroll'))
                }
            }
            this.mounted = true
        },
        beforeDestroy() {
            this.slider.removeEventListener('scroll', this.scroll)
        },
        methods: {
            scroll(event) {
                this.position = event.currentTarget.scrollLeft

                this.showLeft = this.position
                this.showRight = (this.slider.offsetWidth + this.position) < this.slider.scrollWidth - 1
            },

            navigate(index) {
                this.slider.scrollTo(this.slider.childNodes[0]?.offsetWidth * index, 0)
            }
        },
        computed: {
            slider() {
                return this.$scopedSlots.default()[0].context.$refs[this.reference]
            },
            currentSlide() {
                if (this.mounted) {
                    return Math.round(this.position / this.slider.childNodes[0]?.offsetWidth)
                }
            },
            slidesTotal() {
                if (this.mounted) {
                    return Math.round(this.slider.scrollWidth / this.slider.offsetWidth)
                }
            }
        }
    }
</script>
