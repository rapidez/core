<template>
    <div v-if="data?.length">
        <slot :data="data" :components="childComponents"></slot>
    </div>
</template>

<script>

export default {
    props: {
        data: {
            type: Array,
            required: true,
        },
    },
    computed: {
        childComponents() {
            const self = this;

            return this.data.map((item) => {
                return {
                    render: (h) => {
                        if(!item.data?.length) {
                            return h('span');
                        }

                        return h('recursion', {
                            props: {
                                data: item.data,
                            },
                            scopedSlots: self.$scopedSlots
                        });
                    }
                }
            })
        },
    },
}
</script>
