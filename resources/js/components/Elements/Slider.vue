<script>
    import { useElementHover, useIntervalFn, useEventListener } from '@vueuse/core'

    export default {
        render() {
            return this.$scopedSlots.default({
                navigate: this.navigate,
                showLeft: this.showLeft,
                showRight: this.showRight,
                currentSlide: this.currentSlide,
                slidesTotal: this.slidesTotal,
            })
        },
        props: {
            reference: String,
            vertical: {
                type: Boolean,
                default: false
            },
            interval: {
                type: Number,
                default: 5000,
            },
            autoplay: {
                type: Boolean,
                default: false,
            },
            stopOnHover: {
                type: Boolean,
                default: true,
            },
        },
        data: () => {
            return {
                position: 0,
                showLeft: false,
                showRight: false,
                mounted: false,
                hover: false,
                pause: ()=>{},
                resume: ()=>{}
            }
        },
        mounted() {
            useEventListener(this.slider, 'scroll', this.scroll)
            this.slider.dispatchEvent(new CustomEvent('scroll'))
            this.mounted = true

            if (this.stopOnHover){
                this.hover = useElementHover(this.slider);
            }

            if (this.autoplay) {
                let { pause, resume } = useIntervalFn(this.autoScroll, this.interval)
                this.pause = pause
                this.resume = resume
            }
        },
        methods: {
            scroll(event) {
                this.position  = this.vertical ? event.currentTarget.scrollTop : event.currentTarget.scrollLeft
                this.showLeft = this.position
                this.showRight = (this.slider.offsetWidth + this.position) < this.slider.scrollWidth - 1
            },

            autoScroll() {
                let next = this.currentSlide + 1
                if (next >= this.slidesTotal) {
                    next = 0
                }
                this.navigate(next)
            },

            navigate(index) {
                this.vertical
                    ? this.slider.scrollTo(0, this.slider.children[index]?.offsetTop)
                    : this.slider.scrollTo(this.slider.children[0]?.offsetWidth * index, 0)
            }
        },
        watch: {
            hover(isHovering){
                if (!isHovering || !this.stopOnHover) {
                    this.resume()
                } else {
                    this.pause()
                }
            }
        },
        computed: {
            slider() {
                if(!this.reference) {
                    return this.$el;
                }
                return this.$scopedSlots.default()[0].context.$refs[this.reference]
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
            },
        },
    }
</script>
