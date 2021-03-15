<script>
    export default {
        props: ['items'],
        data: () => ({
            skus: ''
        }),
        render() {
            return this.$scopedSlots.default({
                skus: this.skus
            })
        },
        created() {
            let skus = Object.keys(this.items).map((key, index) => this.items[key].sku.split('-')[0]);
            skus.forEach((value, key) => {
                console.log(key)
                console.log(value)
                console.log(skus.length)
                this.skus += key === 0 ? '[' : ''
                this.skus += '"' + value + '"' + (key === (skus.length-1) ? '' : ',')
                this.skus += key === (skus.length -1) ? ']' : ''
            })
            console.log(this.skus)
        }
    }
</script>
