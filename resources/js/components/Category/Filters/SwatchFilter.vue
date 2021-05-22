<template>
    <div v-if="loaded">
        <slot :swatches="swatches"></slot>
    </div>
</template>

<script>
    export default {
        data: () => ({
            swatches: [],
            loaded: false,
        }),

        mounted() {
            if (localStorage.swatches) {
                this.swatches = JSON.parse(localStorage.swatches)
                this.loaded = true
                return
            }

            axios.get('/api/swatches')
                .then((response) => {
                    this.swatches = response.data
                    localStorage.swatches = JSON.stringify(this.swatches)
                    this.loaded = true
                })
                .catch((error) => {
                    Notify(window.config.errors.wrong, 'error')
                })
        }
    }
</script>
