<x-rapidez::breadcrumbs>
    @foreach($category->subcategories as $subcategory)
        <x-rapidez::breadcrumb
            :url="$subcategory->url"
            :active="$subcategory->entity_id == $category->entity_id"
            :position="$loop->iteration + 1"
        >
            {{ $subcategory->name }}
        </x-rapidez::breadcrumb>
    @endforeach
</x-rapidez::breadcrumbs>
