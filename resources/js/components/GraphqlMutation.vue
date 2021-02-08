<script>
import InteractWithUser from './User/mixins/InteractWithUser'
import Login from "./mixins/Login"
import Graphql from "./mixins/Graphql"
import GetCart from "./Cart/mixins/GetCart"

export default {
    mixins: [InteractWithUser, Login, Graphql, GetCart],

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
        async beforeMutate()
        {
            return this.before ? await this[this.before](this.beforeParams) : true
        },

        async afterMutate(afterParams)
        {
            return this.after ? await this[this.after](afterParams) : ''
        },
        async mutate()
        {
            let before = await this.beforeMutate()
            if (!before) {
                return
            }

            delete this.changes.id
            try {
                let response = await this.doGraphqlRequest(this.query)

                if (response.data.errors) {
                    alert(response.data.errors[0].message)
                    return
                }

                if (this.refreshUserInfo) {
                    await this.refreshUser()
                }

                this.mutated = true
                this.afterParams.changes = this.changes

                await this.afterMutate(this.afterParams)


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
