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
            this.formatSkus()
        },
        methods: {
            formatSkus() {
                let skus = Object.keys(this.items).map((key, index) => this.items[key].sku.split('-')[0]);
                skus.forEach((value, key) => {
                    this.skus += key === 0 ? '[' : ''
                    this.skus += '"' + value + '"' + (key === (skus.length-1) ? '' : ',')
                    this.skus += key === (skus.length -1) ? ']' : ''
                })
            }
        }
    }
</script>
