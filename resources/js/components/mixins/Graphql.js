export default {
    methods: {
        async doGraphqlRequest(query) {
            return await axios.post(config.magento_url + '/graphql', {
                query: query.replace('changes', this.queryfy(this.changes))
            }, this.$root.user ? { headers: { Authorization: `Bearer ${localStorage.token}` }} : null)
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