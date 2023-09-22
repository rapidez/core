<script>
import GetCart from './../Cart/mixins/GetCart'
import InteractWithUser from './../User/mixins/InteractWithUser'
import { useLocalStorage } from '@vueuse/core'

export default {
    mixins: [GetCart, InteractWithUser],

    props: {
        checkoutLogin: {
            type: Boolean,
            default: true,
        },
        redirect: {
            type: String,
            default: '/account',
        },
    },

    data: () => ({
        email: useLocalStorage('email').value,
        password: '',
        emailAvailable: true,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        this.refreshUser(false)
        if (this.$root.loggedIn) {
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
                    if (self.$root.cart?.entity_id) {
                        await self.linkUserToCart()
                    } else {
                        await self.refreshCart()
                    }

                    this.successfulLogin()
                })
            } else if (this.email) {
                this.checkEmailAvailability()
            } else {
                Notify(window.config.translations.account.email, 'error')
            }
        },

        checkEmailAvailability() {
            magento
                .post('customers/isEmailAvailable', {
                    customerEmail: this.email,
                })
                .then((response) => {
                    if ((this.emailAvailable = response.data)) {
                        this.$root.guestEmail = this.email
                        this.$root.checkout.step = 2
                    } else {
                        this.$nextTick(function () {
                            this.$scopedSlots.default()[0].context.$refs.password.focus()
                        })
                    }
                })
                .catch((error) => Notify(error.response.data.message, 'error', error.response.data?.parameters))
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
                Turbo.visit(window.url(this.redirect))
            }
        },
    },
}
</script>
