<script>
import { useLocalStorage, StorageSerializers } from '@vueuse/core'

export default {
    props: {
        max: {
            type: Number,
            default: 5,
        },
    },

    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            storage: {},
        }
    },

    mounted() {
        this.storage = useLocalStorage('recently-viewed', { products: [] }, { serializer: StorageSerializers.object }).value

        if (window?.config?.product?.entity_id) {
            this.add(window.config.product.entity_id)
        }
    },

    methods: {
        add(id) {
            if (!this.storage.products.includes(id)) {
                this.storage.products.push(id)
            }

            if (this.storage.products.length > this.max) {
                this.storage.products = this.storage.products.slice(-this.max)
            }
        },

        sort(items) {
            return items.sort((a, b) => this.products.indexOf(b.entity_id) - this.products.indexOf(a.entity_id))
        },
    },

    computed: {
        products() {
            return this.storage.products
        },
    },
}
</script>
