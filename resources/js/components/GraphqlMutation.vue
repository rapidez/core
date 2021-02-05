<script>
import InteractWithUser from './User/mixins/InteractWithUser'
import CheckPassword from "./mixins/CheckPassword"
import Login from "./mixins/Login"
import Graphql from "./mixins/Graphql"
import GetCart from "./Cart/mixins/GetCart"

export default {
    mixins: [InteractWithUser, CheckPassword, Login, Graphql, GetCart],

    props: {
        query: {
            type: String,
            required: true,
        },
        changes: {
            type: Object,
            default: () => ({}),
        },
        refreshUserInfo: {
            type: Boolean,
            default: false,
        },
        redirect: {
            type: String,
            default: '',
        },
        beforeParams: {
            type: Object,
            default: () => ({}),
        },
        afterParams: {
            type: Object,
            default: () => ({}),
        },
        before: {
            type: String
        },
        after: {
            type: String
        }
    },

    data: () => ({
        mutated: false,
    }),

    render()
    {
        return this.$scopedSlots.default({
            changes: this.changes,
            mutate: this.mutate,
            mutated: this.mutated,
            beforeParams: this.beforeParams
        })
    },
    methods: {
        beforeMutate()
        {
            if (this.before) {
                if (!this[this.before](this.beforeParams)) {
                    return false
                }
            }

            return true
        },

        afterMutate(response)
        {
            if (this.after) {
                this[this.after](response);
            }
        },
        async mutate()
        {
            if (!this.beforeMutate()) {
                return
            }
            delete this.changes.id
            try {
                let response = await this.doGraphqlMutation()

                if (response.data.errors) {
                    alert(response.data.errors[0].message)
                    return
                }


                if (this.refreshUserInfo) {
                    this.refreshUser()
                }

                var $me = this
                $me.mutated = true
                $me.afterParams.changes = this.changes

                $me.afterMutate($me.afterParams);


                if (this.redirect) {
                    Turbolinks.visit(this.redirect)
                }
            } catch (e) {
                alert('Something went wrong, please try again')
            }
        }
    }
}
</script>
