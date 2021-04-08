<script>
    import GraphqlMutation from 'Vendor/rapidez/core/resources/js/components/GraphqlMutation.vue';

    export default {
        extends: GraphqlMutation,
        props: {
            checks: {
                type: Object,
                default: () => ({}),
            },
        },
        render() {
            return this.$scopedSlots.default({
                changes: this.changes,
                checks: this.checks,
                mutate: this.mutate,
                mutated: this.mutated,
                error: this.error,
                checkPassword: this.checkPassword
            })
        },
        methods: {
            checkPassword() {
                if(this.changes.password !== this.checks.checkPassword) {
                    this.error = 'Passwords don\'t match.'
                    if(this.alert) alert(this.error)
                } else {
                    this.mutate()
                }
            }
        }
    }
</script>
