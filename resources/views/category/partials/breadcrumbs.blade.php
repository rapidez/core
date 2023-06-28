<x-rapidez::breadcrumbs>
    @foreach($category->parentcategories as $parentcategory)
        <x-rapidez::breadcrumb
            :url="to($parentcategory->url)"
            :active="$parentcategory->entity_id == $category->entity_id"
            :position="$loop->iteration + 1"
        >
            {{ $parentcategory->name }}
        </x-rapidez::breadcrumb>
    @endforeach
</x-rapidez::breadcrumbs>
