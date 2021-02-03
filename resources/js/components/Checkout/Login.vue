<script>
    import GetCart from './../Cart/mixins/GetCart'
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [GetCart, InteractWithUser],

        props: {
            checkoutLogin: {
                type: Boolean,
                default: true,
            }
        },

        data: () => ({
            email: window.debug ? 'roy@justbetter.nl' : '',
            password: '',
            emailAvailable: true,
        }),

        render() {
            return this.$scopedSlots.default({
                loginInputChange: this.loginInputChange,
                email: this.email,
                emailAvailable: this.emailAvailable,
                password: this.password,
                go: this.go,
            })
        },

        created() {
            this.refreshUser(false)
            if (this.$root.user) {
                this.successfulLogin()
            }
        },

        methods: {
            go() {
                if (!this.checkoutLogin && (!this.email || !this.password)) {
                    alert('You did not specify an email or password')
                    return
                }

                if (this.email && this.password) {
                    let self = this
                    this.login(this.email, this.password, async () => {
                        await self.refreshUser(false)
                        if (self.$root.cart) {
                            await self.linkUserToCart()
                            localStorage.mask = self.$root.cart.entity_id
                        }
                        this.successfulLogin()
                    });
                } else if (this.email) {
                    this.checkEmailAvailability()
                } else {
                    alert('A email address is required')
                }
            },

            checkEmailAvailability() {
                magento.post('customers/isEmailAvailable', {
                    customerEmail: this.email
                })
                .then((response) => {
                    if (this.emailAvailable = response.data) {
                        this.$root.guestEmail = this.email
                        this.$root.checkout.step = 2
                    } else {
                        this.$nextTick(function() {
                            this.$scopedSlots.default()[0].context.$refs.password.focus()
                        })
                    }
                })
                .catch((error) => alert(error.response.data.message))
            },

            loginInputChange(e) {
                if (e.target.id == 'email') {
                    this.emailAvailable = true
                }
                this[e.target.id] = e.target.value
            },

            successfulLogin() {
                if (this.checkoutLogin) {
                    this.$root.checkout.step = 2
                } else {
                    Turbolinks.visit('/account')
                }
            }
        }
    }
</script>
