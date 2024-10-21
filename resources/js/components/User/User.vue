<script>
import { user } from '../../stores/useUser';

export default {
    data: function () {
        return {
            user: user,
        }
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    methods: {
        logout(redirect = false) {
            this.$root.$emit('logout', { redirect: redirect })
        },
    },
}
</script>
