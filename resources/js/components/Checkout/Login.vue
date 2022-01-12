<script>
    import GetCart from './../Cart/mixins/GetCart'
    import InteractWithUser from './../User/mixins/InteractWithUser'

    export default {
        mixins: [GetCart, InteractWithUser],

        props: {
            checkoutLogin: {
                type: Boolean,
                default: true,
            },
            redirect: {
                type: String,
                default: '/account'
            }
        },

        data: () => ({
            email: localStorage.email ?? (window.debug ? 'wayne@enterprises.com' : ''),
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
            async go() {
                if (!this.checkoutLogin && (!this.email || !this.password)) {
                    Notify(window.config.translations.account.email_password)
                    return
                }

                if (this.email && this.password) {
                    let self = this
                    await this.login(this.email, this.password, async () => {
                        if (self.$root.cart) {
                            await self.linkUserToCart()
                            localStorage.mask = self.$root.cart.entity_id
                        } else {
                            await self.refreshCart()
                        }

                        this.successfulLogin()
                    });
                } else if (this.email) {
                    this.checkEmailAvailability()
                } else {
                    Notify(window.config.translations.account.email, 'error')
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
                } else if (this.redirect) {
                    Turbolinks.visit(this.redirect)
                }
            }
        },
        watch: {
            email: function () {
                localStorage.email = this.email
            }
        }
    }
</script>
