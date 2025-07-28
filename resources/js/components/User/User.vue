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
            document.dispatchEvent(new CustomEvent('rapidez:logout', { detail: { redirect: redirect } }));
        },
    },
}
</script>
