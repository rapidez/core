<script>
export default {
    props: {
        setQuery: Function,
        query: Object
    },
    mounted() {
        this.setQuery({
            query: this.query
        });
    },
    render: h => null
};
</script>
