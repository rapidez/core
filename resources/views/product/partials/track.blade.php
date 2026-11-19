<script async>
if (! document.documentElement.hasAttribute('data-turbo-preview')) {
    document.addEventListener('vue:loaded', function() {
        window.rapidezAPI('POST', '/track/product/' + {{ $id }})
    }, { once: true })
}
</script>
