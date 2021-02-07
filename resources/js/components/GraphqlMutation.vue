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

        afterMutate(afterParams)
        {
            if (this.after) {
                this[this.after](afterParams);
            }
        },
        async mutate()
        {
            if (!this.beforeMutate()) {
                return
            }
            delete this.changes.id
            try {
                let response = await this.doGraphqlMutation(this.query)

                if (response.data.errors) {
                    alert(response.data.errors[0].message)
                    return
                }


                if (this.refreshUserInfo) {
                    this.refreshUser()
                }

                this.mutated = true
                this.afterParams.changes = this.changes

                this.afterMutate(this.afterParams)

                if (this.redirect) {
                    //Turbolinks.visit('/account')
                }

            } catch (e) {
                console.log(e)
                alert('Something went wrong, please try again')
            }
        }
    }
}
</script>
