<template>
    <div>
        <slot :data="data" :component="childComponent" />
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
        childComponent() {
            return {
                render: (h) =>
                    h('recursion', {
                        props: {
                            data: this.data,
                        },
                    }),
            }
        },
    },
}
</script>
