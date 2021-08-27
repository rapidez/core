<x-rapidez::productlist field="{{ $condition->attribute }}.keyword" :value="array_map('trim', explode(',', $condition->value))" :size="$options->products_count"/>
