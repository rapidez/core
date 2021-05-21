<script>
    export default {
        props: ['notification'],
        render() {
            return this.$scopedSlots.default({
                message: this.message,
                type: this.type,
                show: this.show,
                classType: this.classType,
                icon: this.icon,
                close: this.close
            })
        },
        created() {
            this.message = this.notification.message
            this.type = this.notification.type
            this.show = this.notification.show
        },
        mounted() {
            setTimeout(() => {
                this.close()
            }, 5000)
        },
        data: () => ({
            message: null,
            type: null,
            show: null,
            types: {
                'success': {
                  'class': 'text-green-600 bg-green-100 border border-green-300',
                  'icon': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />'
                },
                'error':{
                    'class': 'text-red-600 bg-red-100 border border-red-300',
                    'icon': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                },
                'info': {
                    'class': 'text-blue-600 bg-blue-100 border border-blue-300',
                    'icon': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'
                },
                'warning': {
                    'class': 'text-yellow-600 bg-yellow-100 border border-yellow-300',
                    'icon': '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />'
                }
            },
        }),
        methods: {
            close() {
                this.show = false
            }
        },
        computed: {
            classType() {
                return this.types[this.type].class
            },

            icon() {
                return this.types[this.type].icon
            }
        }
    }
</script>
