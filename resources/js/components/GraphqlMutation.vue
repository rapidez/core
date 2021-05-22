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
            },
            alert: {
                type: Boolean,
                default: true,
            },
            clear: {
                type: Boolean,
                default: false,
            },
            notify: {
                type: Object,
                default: () => ({
                    message: '',
                    type: 'success'
                })
            }
        },

        data: () => ({
            error: false,
            mutated: false,
        }),

        render() {
            return this.$scopedSlots.default({
                changes: this.changes,
                mutate: this.mutate,
                mutated: this.mutated,
                error: this.error,
            })
        },

        methods: {
            async mutate() {
                delete this.changes.id
                this.error = false

                try {
                    let response = await axios.post(config.magento_url + '/graphql', {
                        query: this.query.replace('changes', this.queryfy(this.changes))
                    }, this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null)

                    if (response.data.errors) {
                        this.error = response.data.errors[0].message
                        if (this.alert) {
                            Notify(response.data.errors[0].message, 'error')
                        }
                        return
                    }

                    if (this.clear) {
                        this.changes = {}
                    }

                    if (this.refreshUserInfo) {
                        this.refreshUser()
                    }

                    var self = this
                    self.mutated = true
                    setTimeout(function() {
                        self.mutated = false
                    }, 2500);

                    if (!this.redirect && this.notify.message) {
                        Notify(this.notify.message, this.notify.type ?? 'success')
                    }

                    if (this.redirect) {
                        if (this.notify.message) {
                            setTimeout(() => {
                                Notify(this.notify.message, this.notify.type ?? 'success')
                            }, 1500)
                        }

                        Turbolinks.visit(this.redirect)
                    }
                } catch (e) {
                    Notify(window.config.translations.errors.wrong, 'warning')
                }
            },

            // Credits: https://stackoverflow.com/a/54262737/622945
            queryfy (obj, key = null) {
                if (typeof obj === 'number') {
                    return obj
                }

                if (obj === null) {
                    return JSON.stringify('')
                }

                if (typeof obj !== 'object' || Array.isArray(obj)) {
                    return key == 'country_code' ? obj : JSON.stringify(obj)
                }

                let props = Object.keys(obj).map(key =>
                     `${key}:${this.queryfy(obj[key], key)}`
                ).join(',')

                return Object.keys(this.changes).length === 1
                    ? props
                    : `{${props}}`
            }
        }
    }
</script>
