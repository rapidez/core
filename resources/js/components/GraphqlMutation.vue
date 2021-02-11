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
                default: () => ({}),
            },
            refreshUserInfo: {
                type: Boolean,
                default: false,
            },
            redirect: {
                type: String,
                default: '',
            }
        },

        data: () => ({
            mutated: false,
        }),

        render() {
            console.log(this.changes)
            return this.$scopedSlots.default({
                changes: this.changes,
                mutate: this.mutate,
                mutated: this.mutated,
            })
        },

        methods: {
            async mutate() {
                delete this.changes.id

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
                    console.log(response)
                    var me = this
                    me.mutated = true
                    setTimeout(function(){
                        me.mutated = false
                    }, 2500);

                    if (this.redirect) {
                        Turbolinks.visit(this.redirect)
                    }
                } catch (e) {
                    alert('Something went wrong, please try again')
                }
            },

            // Credits: https://stackoverflow.com/a/54262737/622945
            queryfy (obj, key = null) {
                if(typeof obj === 'number') {
                    return obj
                }

                if (obj === null) {
                    return JSON.stringify('')
                }

                if(typeof obj !== 'object' || Array.isArray( obj )) {
                    return key == 'country_code' ? obj : JSON.stringify( obj )
                }

                let props = Object.keys(obj).map(key =>
                     `${key}:${this.queryfy(obj[key], key)}`
                ).join(',')

                return `{${props}}`
            }
        }
    }
</script>
