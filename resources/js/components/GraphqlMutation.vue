<script>
    import InteractWithUser from './User/mixins/InteractWithUser'

    export default {
        mixins: [InteractWithUser],

        props: {
            query: {
                type: String,
                required: true,
            },
            changes: {
                type: Object,
                required: true,
            },
            refreshUserInfo: {
                type: Boolean,
                default: false,
            },
        },

        data: () => ({
            mutated: false,
        }),

        render() {
            return this.$scopedSlots.default({
                changes: this.changes,
                mutate: this.mutate,
                mutated: this.mutated,
            })
        },

        methods: {
            async mutate() {
                try {
                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query.replace('changes', this.queryfy(this.changes))
                    }, this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null)

                    if (response.data.errors) {
                        alert(response.data.errors[0].message)
                        return
                    }

                    if (this.refreshUserInfo) {
                        this.refreshUser()
                    }

                    var me = this
                    me.mutated = true
                    setTimeout(function(){
                        me.mutated = false
                    }, 2500);
                } catch (e) {
                    alert('Something went wrong, please try again')
                }
            },

            // Credits: https://stackoverflow.com/a/54262737/622945
            queryfy (obj) {
                if( typeof obj === 'number' ) {
                    return obj;
                }

                if( typeof obj !== 'object' || Array.isArray( obj ) ) {
                    return JSON.stringify( obj );
                }

                let props = Object.keys( obj ).map( key =>
                    `${key}:${this.queryfy( obj[key] )}`
                ).join( ',' );

                return `{${props}}`;
            }
        }
    }
</script>
