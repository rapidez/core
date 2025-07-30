<x-rapidez::productlist :title="$options->title ?? false" field="{{ $condition->attribute }}" :value="array_map('trim', explode(',', $condition->value))"/>
