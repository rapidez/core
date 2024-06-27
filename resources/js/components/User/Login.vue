<script>
import { useLocalStorage } from '@vueuse/core'
import { user, login } from '../../stores/useUser'

export default {
    props: {
        redirect: {
            type: String,
            default: '/account',
        },
    },

    data: () => ({
        email: useLocalStorage('email').value,
        password: ''
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        if (user?.value?.id) {
            this.successfulLogin()
        }
    },

    methods: {
        async go() {
            if (!this.email || !this.password) {
                Notify(window.config.translations.account.email_password)
                return false
            }

            if(await login(this.email, this.password)) {
                this.successfulLogin()
                return true;
            }
            return false
        },

        successfulLogin() {
            Turbo.visit(window.url(this.redirect))
        },
    }
}
</script>
