<script>
export default {
    render() {
        return this.$scopedSlots.default(this)
    },

    computed: {
        shouldFormatCurrencyLeft() {
            return new Intl.NumberFormat(config.locale.replace('_', '-'), {
                style: 'currency',
                currency: config.currency,
            }).formatToParts(1)?.[0]?.type === 'currency'
        },

        currencySymbol() {
            return new Intl.NumberFormat(config.locale.replace('_', '-'), {
                style: 'currency',
                currency: config.currency,
                maximumFractionDigits: 0,
            }).format(0).replace(/\d/g, '').trim()
        },
    }
}
</script>
