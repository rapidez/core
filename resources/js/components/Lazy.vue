<!-- Copied from https://github.com/RadKod/v-lazy-component and modified it -->
<template>
    <component
        :is="state.wrapperTag"
        :class="[
            'v-lazy-component',
            {
                'v-lazy-component--loading': !state.isLoaded,
                'v-lazy-component--loaded': state.isLoaded,
            },
        ]"
        :style="{
            minWidth: '1px',
            minHeight: '1px',
        }"
    >
        <slot v-if="state.isLoaded" :loaded="state.isLoaded" :intersected="state.isIntersected" />
        <!-- Content that is loaded as a placeholder until it comes into view -->
        <slot v-if="!state.isLoaded" name="placeholder" />
    </component>
</template>

<script>
export default {
    props: {
        wrapperTag: {
            type: String,
            required: false,
            default: 'div',
        },
        isIntersected: {
            type: Boolean,
            required: false,
            default: false,
        },
        isLoaded: {
            type: Boolean,
            required: false,
            default: false,
        },
        idle: {
            type: Boolean,
            required: false,
            default: false,
        },
        idleTimeout: {
            type: Number,
            required: false,
            default: 3000,
        },
        /**
         * See IntersectionOberserver rootMargin [docs](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API#Intersection_observer_options)
         */
        rootMargin: {
            type: String,
            required: false,
            default: '0px 0px 0px 0px',
        },
        /**
         * See IntersectionOberserver treshold [docs](https://developer.mozilla.org/en-US/docs/Web/API/Intersection_Observer_API#Intersection_observer_options)
         */
        threshold: {
            type: [Number, Array],
            required: false,
            default: 0,
        },
    },
    data() {
        return {
            state: {
                wrapperTag: this.wrapperTag,
                isIntersected: this.isIntersected,
                isLoaded: this.isIntersected || this.isLoaded,
                idle: this.idle,
                rootMargin: this.rootMargin,
                threshold: this.threshold,
                observer: null,
            },
        }
    },
    watch: {
        isIntersected(value) {
            if (value) {
                this.state.isIntersected = true
                this.state.isLoaded = true
            }
        },
        isLoaded(value) {
            if (value) {
                this.state.isLoaded = true
            }
        },
        'state.isIntersected'(value) {
            if (value) {
                this.$emit('intersected', this.$el)
            }
        },
        'state.isLoaded'(value) {
            if (value) {
                this.$emit('loaded', this.$el)
            }
        },
    },
    mounted() {
        if ('IntersectionObserver' in window) {
            if (!this.state.isIntersected && !this.state.idle) {
                this.observe()
            }
        }

        this.onIdle(() => {
            this.state.isLoaded = true
        })

        if (this.state.isIntersected) {
            this.$emit('intersected', this.$el)
        }

        if (this.state.isLoaded) {
            this.$emit('loaded', this.$el)
        }
    },
    beforeDestroy() {
        if (!this.state.isIntersected && !this.state.idle) {
            this.unobserve()
        }
    },
    methods: {
        onIdle(callback) {
            if (this.idleTimeout && this.idleTimeout > 0) {
                setTimeout(() => {
                    this.$nextTick(callback)
                }, this.idleTimeout)
            }
        },
        observe() {
            const { rootMargin, threshold } = this.state
            const config = { root: undefined, rootMargin, threshold }
            this.state.observer = new IntersectionObserver(this.onIntersection, config)
            this.state.observer.observe(this.$el)
        },
        onIntersection(entries) {
            this.state.isIntersected = entries.some((entry) => entry.intersectionRatio > 0)
            this.state.isLoaded = this.state.isIntersected
            if (this.state.isLoaded) {
                this.unobserve()
            }
        },
        unobserve() {
            if ('IntersectionObserver' in window) {
                this.state.observer.unobserve(this.$el)
            }
        },
    },
}
</script>
