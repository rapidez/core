<x-rapidez::notifications />
<footer class="bg-white">
    <div class="container mx-auto">
        <div class="py-12 lg:py-16">
            @if(Route::currentRouteName() !== 'checkout')
                <div class="xl:grid xl:grid-cols-3 xl:gap-8">
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
    </div>
</footer>
