<script>
import InteractWithUser from './../User/mixins/InteractWithUser'
import { useLocalStorage } from '@vueuse/core'

export default {
    mixins: [InteractWithUser],

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
    },

    mounted() {
        if (this.$root.loggedIn) {
            this.successfulLogin()
        } else if (this.email) {
            this.checkEmailAvailability()
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
                    this.successfulLogin()
                })
            } else if (this.email) {
                await this.checkEmailAvailability()

                if (this.emailAvailable) {
                    this.$root.guestEmail = this.email
                    this.$root.checkout.step = this.nextStep
                } else {
                    this.$nextTick(function () {
                        this.$scopedSlots.default()[0].context.$refs.password.focus()
                    })
                }
            } else {
                Notify(window.config.translations.account.email, 'error')
            }
        },

        async checkEmailAvailability() {
            this.emailAvailable = await window.magentoAPI('post', 'customers/isEmailAvailable', {
                customerEmail: this.email,
            })
        },

        loginInputChange(e) {
            if (e.target.id == 'email') {
                this.emailAvailable = true
            }
            this[e.target.id] = e.target.value
        },

        successfulLogin() {
            if (this.checkoutLogin) {
                this.$root.checkout.step = this.nextStep
            } else if (this.redirect) {
                Turbo.visit(window.url(this.redirect))
            }
        },
    },
    computed: {
        loginStep: function () {
            return this.$root.getCheckoutStep('login')
        },
        nextStep: function () {
            return this.loginStep + 1
        },
    },
}
</script>
