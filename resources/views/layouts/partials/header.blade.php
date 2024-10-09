<header class="relative py-2 shadow mb-5 border-b">
    <div class="container flex flex-wrap items-center">
        <input id="navigation" class="peer hidden" type="checkbox" />
        <div class="py-2">
            <div class="mr-3 flex items-center text-xl sm:text-3xl">
                <a href="{{ url('/') }}" aria-label="@lang('Go to home')">
                    <span class="hidden sm:inline">
                        <img src="https://raw.githubusercontent.com/rapidez/art/master/logo.svg" alt="Rapidez logo" height="48" width="152">
                    </span>
                    <span class="inline sm:hidden">
                        <img src="https://raw.githubusercontent.com/rapidez/art/master/r.svg" alt="Rapidez logo" height="30" width="30">
                    </span>
                </a>
                <label for="navigation" class="ml-3 cursor-pointer sm:hidden">
                    <x-heroicon-o-bars-3 class="inline w-7" />
                </label>
            </div>
        </div>
        <div class="flex items-center h-12 max-md:order-last max-md:w-full md:max-w-lg md:flex-1">
            @include('rapidez::layouts.partials.header.autocomplete')
        </div>
        <div class="ml-auto flex items-center justify-end pl-3">
            @include('rapidez::layouts.partials.header.account')
            @include('rapidez::layouts.partials.header.minicart')
        </div>
        <nav class="w-full bg-white max-md:inset-x-0 max-md:top-12 max-md:absolute max-md:z-40 max-md:grid max-md:grid-rows-[0fr] max-md:peer-checked:border-b max-md:shadow max-md:transition-all max-md:peer-checked:grid-rows-[1fr]">
            <div class="max-h-full overflow-hidden">
                {{-- Because the lack of an @includeIf or @includeWhen equivalent for Blade components we're using a placeholder --}}
                <x-dynamic-component :component="App::providerIsLoaded('Rapidez\Menu\MenuServiceProvider') ? 'menu' : 'placeholder'" />
            </div>
        </nav>
    </div>
</header>
