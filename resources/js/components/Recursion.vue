<template>
    <div v-if="data?.length">
        <slot :data="data" :components="childComponents"></slot>
    </div>
</template>

<script>
import { h, resolveComponent } from 'vue'

export default {
    props: {
        data: {
            type: Array,
            required: true,
        },
    },
    computed: {
        childComponents() {
            const self = this

            return this.data.map((item) => {
                return {
                    render: () => {
                        if (!item.data?.length) {
                            return h('span')
                        }

                        return h(resolveComponent('recursion'), { data: item.data }, self.$slots)
                    },
                }
            })
        },
    },
}
</script>
