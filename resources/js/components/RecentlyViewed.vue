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
            if (!this.products.includes(id)) {
                this.products.push(id)
            }

            if (this.products.length > this.max) {
                this.storage.products = this.products.slice(-this.max)
            }
        },
    },

    computed: {
        products() {
            return this.storage.products
        },
    },
}
</script>
