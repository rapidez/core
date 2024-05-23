<!-- TODO: Check if we can delete this and refactor it to the GraphQL mutation component -->
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
                    this.successfulLogin()
                })
            } else if (this.email) {
                await this.checkEmailAvailability()
            } else {
                Notify(window.config.translations.account.email, 'error')
            }
        },

        async checkEmailAvailability() {
            // TODO: If we still need this Vue component it would
            // be nice if it's also going to be a GraphQL call.
            let responseData = await window.magentoAPI('post', 'customers/isEmailAvailable', {
                customerEmail: this.email,
            })

            if ((this.emailAvailable = responseData)) {
                this.$root.guestEmail = this.email
                Turbo.visit(window.url(this.redirect))
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
            Turbo.visit(window.url(this.redirect))
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
