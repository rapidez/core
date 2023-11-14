<script>
import InteractWithUser from './User/mixins/InteractWithUser'
import { useLocalStorage, StorageSerializers } from '@vueuse/core'
import { token } from '../stores/useUser'

export default {
    mixins: [InteractWithUser],

    props: {
        query: {
            type: String,
            required: true,
        },
        variables: {
            type: Object,
            default: () => ({}),
        },
        check: {
            type: String,
        },
        redirect: {
            type: String,
        },
        cache: {
            type: String,
        },
        callback: {
            type: Function,
        },
    },

    data: () => ({
        data: null,
        cachePrefix: 'graphql_',
    }),

    render() {
        return this.$scopedSlots.default(this)
    },

    created() {
        if (!this.getCache()) {
            this.runQuery()
        }
    },

    methods: {
        getCache() {
            if (this.cache === undefined) {
                return false
            }
            this.data = useLocalStorage(this.cachePrefix + this.cache, null, { serializer: StorageSerializers.object }).value

            return this.data
        },

        async runQuery() {
            try {
                let options = { headers: {} }

                if (token.value) {
                    options['headers']['Authorization'] = `Bearer ${token.value}`
                }

                if (window.config.store_code) {
                    options['headers']['Store'] = window.config.store_code
                }

                let response = await axios.post(
                    config.magento_url + '/graphql',
                    {
                        query: this.query,
                        variables: this.variables,
                    },
                    options,
                )

                if (response.data.errors) {
                    if (response.data.errors[0].extensions.category == 'graphql-authorization') {
                        this.logout(window.url('/login'))
                    } else {
                        Notify(response.data.errors[0].message, 'error')
                    }
                    return
                }

                if (this.check) {
                    if (!eval('response.data.' + this.check)) {
                        Turbo.visit(window.url(this.redirect))
                        return
                    }
                }

                this.data = this.callback ? await this.callback(this.data, response) : response.data.data

                if (this.cache) {
                    useLocalStorage(this.cachePrefix + this.cache, null, { serializer: StorageSerializers.object }).value = this.data
                }
            } catch (e) {
                Notify(window.config.translations.errors.wrong, 'warning')
            }
        },
    },
}
</script>
