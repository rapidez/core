<x-rapidez::notifications />
<x-rapidez::cookie-notice>
    @lang('This website uses cookies')
    <x-slot:button>@lang('Accept cookies')</x-slot:button>
</x-rapidez::cookie-notice>
<footer class="container bg-white mt-auto">
    <div class="mt-8 py-12 pt-8 lg:py-16 border-t">
        <div class="flex flex-col justify-between md:flex-row xl:gap-8">
            @include('rapidez::layouts.partials.footer.navigation')
            @include('rapidez::layouts.partials.footer.newsletter')
        </div>
        <div class="mt-8 border-t py-8 md:flex md:items-center md:justify-between">
            @include('rapidez::layouts.partials.footer.social')
            <div class="mt-8 md:mt-0">
                @include('rapidez::layouts.partials.footer.copyrights')
            </div>
        </div>
    </div>
</footer>
