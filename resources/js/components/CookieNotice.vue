<script>
    import Popup from './Popup.vue'
    export default {
        extends: Popup,
        render() {
            return this.$scopedSlots.default({
                show: this.show,
                close: this.close,
                accept: this.accept,
            })
        },
        methods: {
            accept() {
                document.cookie = 'accept-cookies=true'
                this.close()
                location.reload()
            },
        },
    }
</script>