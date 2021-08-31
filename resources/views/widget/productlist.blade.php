<x-rapidez::productlist :title="$options->title ?? false" field="{{ $condition->attribute }}.keyword" :value="array_map('trim', explode(',', $condition->value))"/>
