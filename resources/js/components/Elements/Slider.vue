<script>
import { useElementHover, useIntervalFn, useEventListener, useThrottleFn, useResizeObserver } from '@vueuse/core'

export default {
    render() {
        return this.$scopedSlots.default(this)
    },
    props: {
        reference: {
            type: String,
            default: 'slider',
        },
        vertical: {
            type: Boolean,
            default: false,
        },
        interval: {
            type: Number,
            default: 5000,
        },
        autoplay: {
            type: Boolean,
            default: false,
        },
        bounce: {
            type: Boolean,
            default: false,
        },
        stopOnHover: {
            type: Boolean,
            default: true,
        },
        loop: {
            type: Boolean,
            default: false,
        },
    },
    data: () => {
        return {
            slider: '',
            position: 0,
            showLeft: false,
            showRight: false,
            mounted: false,
            hover: false,
            direction: 1,
            chunk: '',
            pause: () => {},
            resume: () => {},

            childSpan: 0,
            sliderSpan: 0,
        }
    },
    mounted() {
        this.initSlider()
        useResizeObserver(this.slider, useThrottleFn(this.updateSpan, 150, true, true))
        useEventListener(this.slider, 'scroll', useThrottleFn(this.scroll, 150, true, true), { passive: true })
        this.$nextTick(() => {
            if (this.loop) {
                this.chunk = this.slider.cloneNode(true)
            }
            this.slider.dispatchEvent(new CustomEvent('scroll'))
            this.mounted = true

            this.initAutoPlay()
        })

        this.updateSpan();
    },
    methods: {
        initSlider() {
            this.slider = this.$scopedSlots.default()[0].context.$refs[this.reference]
        },
        initAutoPlay() {
            if (!this.autoplay) {
                return
            }

            const { pause, resume } = useIntervalFn(this.autoScroll, this.interval)
            this.pause = pause
            this.resume = resume

            if (!this.stopOnHover) {
                return
            }
            this.hover = useElementHover(this.$el)
        },
        scroll(event) {
            this.position = this.vertical ? event.target.scrollTop : event.target.scrollLeft
            this.showLeft = this.loop || this.position
            this.showRight = this.loop || this.slider.offsetWidth + this.position < this.slider.scrollWidth - 1
        },
        autoScroll() {
            if (this.slidesTotal == 1) {
                return
            }
            let next = this.currentSlide + this.direction
            if (next >= this.slidesTotal || next < 0) {
                if (this.bounce) {
                    this.direction = -this.direction
                    next = this.currentSlide + this.direction
                } else {
                    next = 0
                }
            }
            this.navigate(next)
        },
        navigate(index, behavior = 'smooth') {
            this.vertical
                ? this.slider.scrollTo({ left: 0, top: this.slider.children[index]?.offsetTop, behavior: behavior })
                : this.slider.scrollTo({ left: this.slider.children[0]?.offsetWidth * index, top: 0, behavior: behavior})
        },
        handleLoop() {
            if (this.currentSlide + 1 === this.slidesTotal - 1) {
                Array.from(this.chunk.children).forEach((child) => {
                    this.slider.appendChild(child.cloneNode(true))
                })
            }
            if (this.currentSlide < 1) {
                Array.from(this.chunk.children).forEach((child) => {
                    this.slider.insertBefore(child.cloneNode(true), this.slider.firstChild)
                })
            }
        },
        updateSpan() {
            let slide = this.childSpan == 0 ? 0 : this.currentSlide

            this.childSpan = this.vertical
                ? this.slider.children[0]?.offsetHeight ?? this.slider.offsetHeight
                : this.slider.children[0]?.offsetWidth ?? this.slider.offsetWidth

            this.navigate(slide, 'instant')

            this.sliderSpan = this.vertical ? this.slider.offsetHeight : this.slider.offsetWidth
        },
    },
    watch: {
        hover(isHovering) {
            if (!isHovering || !this.stopOnHover) {
                this.resume()
            } else {
                this.pause()
            }
        },
        currentSlide() {
            this.initSlider()
            if (this.loop) {
                this.handleLoop()
            }
        },
    },
    computed: {
        currentSlide() {
            if (!this.mounted) {
                return 0
            }

            return Math.round(this.position / this.childSpan)
        },
        slidesVisible() {
            if (!this.mounted) {
                return 0
            }

            return Math.round(this.sliderSpan / this.childSpan)
        },
        slidesTotal() {
            if (!this.mounted) {
                return 0
            }

            return (this.slider.children?.length ?? 1) - this.slidesVisible + 1
        },
    },
}
</script>
