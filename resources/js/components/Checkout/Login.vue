<script>
import { refresh as refreshCart } from '../../stores/useCart'
import { refreshMask } from '../../stores/useMask'
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
                    if (self.$root.cart?.id) {
                        await self.linkUserToCart()
                    } else {
                        // TODO: Get the cart with the "customerCart" query?
                        await refreshMask();
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
            let responseData = window.magentoAPI('post', 'customers/isEmailAvailable', {
                customerEmail: this.email,
            })

            if ((this.emailAvailable = responseData)) {
                this.$root.guestEmail = this.email
                this.$root.checkout.step = 2
            } else {
                this.$nextTick(function () {
                    this.$scopedSlots.default()[0].context.$refs.password.focus()
                })
            }
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
