<x-rapidez::breadcrumbs>
    @foreach($category->subcategories as $subcategory)
        <x-rapidez::breadcrumb :url="$subcategory->url" :active="$subcategory->entity_id == $category->entity_id">
            {{ $subcategory->name }}
        </x-rapidez::breadcrumb>
    @endforeach
</x-rapidez::breadcrumbs>
