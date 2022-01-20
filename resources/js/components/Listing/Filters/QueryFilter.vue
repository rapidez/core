<!-- This could be removed as it's possible to set the query in the template -->
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
