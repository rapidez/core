<x-rapidez::notifications />
<footer class="bg-white">
    <div class="container mx-auto mt-8 border-t border-gray-200 py-12 pt-8 lg:py-16">
        @if (Route::currentRouteName() !== 'checkout')
            <div class="flex flex-col justify-between md:flex-row xl:gap-8">
                @include('rapidez::layouts.partials.footer.navigation')
                @include('rapidez::layouts.partials.footer.newsletter')
            </div>
        @endif
        <div class="mt-8 border-t border-gray-200 pt-8 md:flex md:items-center md:justify-between">
            @includeWhen(Route::currentRouteName() !== 'checkout', 'rapidez::layouts.partials.footer.social')
            <div class="mt-8 md:mt-0">
                @include('rapidez::layouts.partials.footer.copyrights')
            </div>
        </div>
    </div>
</footer>
