<template>
    <turbo-frame :src="src" :id="id" @turbo:before-frame-render="handleBeforeRender" loading="lazy" target="_top">
        <component :is="fetchedComponent"></component>
    </turbo-frame>
</template>

<script>
export default {
    name: 'VueTurboFrame',
    props: {
        src: String,
        id: String,
    },
    data() {
        return {
            html: '<div></div>',
        }
    },
    computed: {
        fetchedComponent() {
            return {
                template: this.html || '',
            }
        },
    },
    methods: {
        handleBeforeRender(event) {
            // override default render function
            event.detail.render = (_currentElement, newElement) => {
                this.html = newElement.innerHTML
            }
        },
    },
}
</script>
