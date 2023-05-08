<header class="relative mb-5 border-b shadow">
    <div class="{{ Route::currentRouteName() == 'checkout' ? 'justify-center' : '' }} container mx-auto flex flex-wrap items-center max-sm:px-3">
        <input
            id="navigation"
            class="peer hidden"
            type="checkbox"
        />
        <div class="py-2">
            <div class="mr-3 flex items-center text-xl sm:text-3xl">
                <a
                    href="/"
                    aria-label="@lang('Go to home')"
                >
                    <span class="hidden sm:inline">
                        <img
                            src="https://raw.githubusercontent.com/rapidez/art/master/logo.svg"
                            alt=""
                            height="48"
                            width="152"
                        >
                    </span>
                    <span class="inline sm:hidden">
                        <img
                            src="https://raw.githubusercontent.com/rapidez/art/master/r.svg"
                            alt=""
                            height="30"
                            width="30"
                        >
                    </span>
                </a>
                <label
                    class="ml-3 cursor-pointer sm:hidden"
                    for="navigation"
                >
                    <x-heroicon-o-menu class="inline w-7" />
                </label>
            </div>
        </div>
        @if (Route::currentRouteName() !== 'checkout')
            <div class="flex h-12 max-w-3xl flex-1 items-center">
                @include('rapidez::layouts.partials.header.autocomplete')
            </div>
            <div class="ml-auto flex justify-end pl-3">
                @include('rapidez::layouts.partials.header.account')
                @if (Route::currentRouteName() !== 'checkout')
                    @include('rapidez::layouts.partials.header.minicart')
                @endif
            </div>
        @endif
        @if (Route::currentRouteName() !== 'checkout')
            <nav class="inset-x-0 top-full z-10 w-full overflow-hidden rounded-b bg-white transition-all peer-checked:max-h-screen max-sm:absolute max-sm:max-h-0 max-sm:border max-sm:shadow">
                {{-- Because the lack of an @includeIf or @includeWhen equivalent for Blade components we're using a placeholder --}}
                <x-dynamic-component :component="App::providerIsLoaded('Rapidez\Menu\MenuServiceProvider') ? 'menu' : 'placeholder'" />
            </nav>
        @endif
    </div>
</header>
