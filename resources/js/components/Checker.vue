<script>
    import InteractWithUser from 'Vendor/rapidez/core/resources/js/components/User/mixins/InteractWithUser'
    import GetCart from 'Vendor/rapidez/core/resources/js/components/Cart/mixins/GetCart'
    export default {
        mixins: [InteractWithUser, GetCart],
        props: {
            parentfunc: {
                type: String
            }
        },
        data: () => ({
            checkVal: '',
        }),
        created() {
            this.$on('checked', async() => {
                await this.$parent.[this.parentfunc]()
                .then(async () => {
                    let self = this
                    await this.login(this.$parent.changes.email, this.$parent.changes.password, async () => {
                        if (self.$root.cart) {
                            await self.linkUserToCart()
                            localStorage.mask = self.$root.cart.entity_id
                        } else {
                            await self.refreshCart()
                        }

                        Turbolinks.visit('/account')
                    })
                })
            })
        },
        render() {
            return this.$scopedSlots.default({
                checkVal: this.checkVal,
                check: this.check,
            })
        },
        methods: {
            async check(value1, value2) {
                if (value1 === value2) {
                    this.$emit('checked')
                } else {
                    alert('Passwords don\'t match.')
                }
            }
        }
    }
</script>
