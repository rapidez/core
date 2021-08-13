<x-rapidez::breadcrumbs>
    @foreach($product->breadcrumb_categories as $category)
        <x-rapidez::breadcrumb :url="$category->url">
            {{ $category->name }}
        </x-rapidez::breadcrumb>
    @endforeach
    <x-rapidez::breadcrumb :active="true">{{ $product->name }}</x-rapidez::breadcrumb>
</x-rapidez::breadcrumbs>
