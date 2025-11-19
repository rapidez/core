<script>
import { user } from '../../stores/useUser'

export default {
    data: function () {
        return {
            user: user,
        }
    },

    render() {
        return this.$slots.default(this)
    },

    methods: {
        logout(redirect = false) {
            window.$emit('logout', { redirect: redirect })
        },
    },
}
</script>
