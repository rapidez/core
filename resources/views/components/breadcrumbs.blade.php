<ol class="flex mb-5" itemscope itemtype="https://schema.org/BreadcrumbList">
    <x-rapidez::breadcrumb :url="url('/')" position="1">@lang('Home')</x-rapidez::breadcrumb>
    {{ $slot }}
</ol>
