<ol
    class="container mb-3 flex"
    itemscope
    itemtype="https://schema.org/BreadcrumbList"
>
    <x-rapidez::breadcrumb
        url="/"
        position="1"
    >@lang('Home')</x-rapidez::breadcrumb>
    {{ $slot }}
</ol>
