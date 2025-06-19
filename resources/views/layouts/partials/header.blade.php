<header class="relative mb-6 max-md:pb-4 border-b shadow z-header pt-2 lg:pt-3">
    <div class="container flex flex-wrap items-center">
        <input id="navigation" class="peer hidden" type="checkbox" />
        <div class="mr-5 flex items-center text-xl max-sm:flex-row-reverse sm:text-3xl">
            <a href="{{ url('/') }}" aria-label="@lang('Go to home')">
                <span class="hidden sm:inline">
                    <img src="https://raw.githubusercontent.com/rapidez/art/master/logo.svg" alt="Rapidez logo" height="48" width="152">
                </span>
                <span class="inline sm:hidden">
                    <img src="https://raw.githubusercontent.com/rapidez/art/master/r.svg" alt="Rapidez logo" height="30" width="30">
                </span>
            </a>
            <label for="navigation" class="mr-3 cursor-pointer sm:hidden">
                <x-heroicon-o-bars-3 class="inline w-7" />
            </label>
        </div>
        <div class="flex items-center h-12 max-md:order-last max-md:w-full md:max-w-lg md:flex-1 max-lg:mt-2">
            @include('rapidez::layouts.partials.header.autocomplete')
        </div>
        <div class="ml-auto flex items-center justify-end pl-3">
            @include('rapidez::layouts.partials.header.account')
            @include('rapidez::layouts.partials.header.minicart')
        </div>
        <nav class="inset-x-0 top-full w-full bg-white max-md:absolute max-md:grid max-md:grid-rows-[0fr] max-md:shadow-md max-md:transition-all max-md:peer-checked:grid-rows-[1fr] max-md:container">
            <div class="max-h-full overflow-hidden">
                {{-- Because the lack of an @includeIf or @includeWhen equivalent for Blade components we're using a placeholder --}}
                <x-dynamic-component :component="App::providerIsLoaded('Rapidez\Menu\MenuServiceProvider') ? 'menu' : 'placeholder'" />
            </div>
        </nav>
    </div>
</header>
