<script>
import { cart, setGuestEmailOnCart } from '../../stores/useCart'
import { isEmailAvailable, login, register, user } from '../../stores/useUser'
import { useDebounceFn } from '@vueuse/core'

const debouncePromise = useDebounceFn(async function (self) {
    self.isEmailAvailable = await isEmailAvailable(self.email || '')
}, 300)

export default {
    props: {
        checkWhileTyping: {
            type: Boolean,
            default: true,
        },
        nextUrl: {
            type: String,
            default: '',
        },
    },

    data: () => ({
        email: cart.value.email,
        password: '',
        password_repeat: '',
        createAccount: false,
        firstname: '',
        lastname: '',
        isEmailAvailable: true,
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    async mounted() {
        if (user.value.is_logged_in && config.checkout_steps[config.store_code].length > 1) {
            this.$root.submitPartials(this.$el.form).then(() => {
                window.Turbo.visit(this.nextUrl)
            })
        }
    },

    methods: {
        async go() {
            if (user.value.is_logged_in) {
                return true
            }

            let isAvailable = await this.checkEmailAvailability()
            if (!isAvailable && !this.password) {
                return false
            }

            if (!isAvailable && this.password) {
                return await this.handleLogin()
            }

            if (this.createAccount) {
                return await this.handleRegister()
            }

            return await this.handleGuest()
        },

        async handleLogin() {
            return await login(this.email, this.password)
                .then(() => true)
                .catch((error) => {
                    if (error.message) {
                        Notify(error.message, 'error')
                    }
                    return false
                })
        },

        async handleRegister() {
            if (this.password !== this.password_repeat) {
                Notify(window.config.translations.account.password_mismatch, 'warning')

                return false
            }
            return await register(this.email, this.firstname, this.lastname, this.password)
                .then(() => true)
                .catch((error) => {
                    if (error.message) {
                        Notify(error.message, 'error')
                    }
                    return false
                })
        },

        async handleGuest() {
            await setGuestEmailOnCart(this.email)

            return true
        },

        async checkEmailAvailability() {
            return await isEmailAvailable(this.email).then((isAvailable) => {
                this.isEmailAvailable = isAvailable
                return isAvailable
            })
        },
    },
    watch: {
        email: async function () {
            if (!this.checkWhileTyping) {
                return
            }
            await debouncePromise(this)
        },
        isEmailAvailable: function (isAvailable) {
            if (!isAvailable) {
                this.createAccount = false
            }
        },
    },
}
</script>
