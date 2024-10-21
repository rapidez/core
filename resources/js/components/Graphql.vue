<script>
import InteractWithUser from './User/mixins/InteractWithUser'
import { useLocalStorage, StorageSerializers } from '@vueuse/core'

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
            type: Function,
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
        errorCallback: {
            type: Function,
            default: (variables, error) => Notify(window.config.translations.errors.wrong, 'warning'),
        },
        store: {
            type: String,
            default: window.config.store_code,
        },
    },

    data: () => ({
        data: null,
        cachePrefix: 'graphql_',
    }),

    render() {
        if (!('default' in this.$scopedSlots)) {
            return null
        }

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
                let options = {
                    headers: {},
                    redirectOnExpiration: true,
                    notifyOnError: true,
                }

                if (this.store) {
                    options['headers']['Store'] = this.store
                }

                let response = await window.magentoGraphQL(this.query, this.variables, options)

                if (this.check) {
                    if (! this.check(response.data)) {
                        Turbo.visit(window.url(this.redirect))
                        return
                    }
                }

                this.data = this.callback ? await this.callback(this.variables, response) : response.data

                if (this.cache) {
                    useLocalStorage(this.cachePrefix + this.cache, null, { serializer: StorageSerializers.object }).value = this.data
                }
            } catch (error) {
                console.error(error)
                this.errorCallback(this.variables, await error?.response?.json())
            }
        },
    },
}
</script>
