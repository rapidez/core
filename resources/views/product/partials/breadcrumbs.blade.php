<x-rapidez::breadcrumbs>
    @foreach ($product->breadcrumb_categories as $category)
        <x-rapidez::breadcrumb :url="url($category->url)" :position="$loop->iteration + 1">
            {{ $category->name }}
        </x-rapidez::breadcrumb>
    @endforeach
    <x-rapidez::breadcrumb :active="true" :position="count($product->breadcrumb_categories) + 2">{{ $product->name }}</x-rapidez::breadcrumb>
</x-rapidez::breadcrumbs>
