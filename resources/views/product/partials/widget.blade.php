<div class="container">
    @widget(
        'content',
        ['all_products', $product->type_id.'_products'],
        ['catalog_product_view', 'catalog_product_view_type_'.$product->type_id],
        $product->entity_id
    )
</div>
