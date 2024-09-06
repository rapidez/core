{{--
    This component adds some styling on the slideover to hide the slideover on desktop and placing the content on the page itself.
    By using this, you have the ability to have something that becomes a slideover on mobile.
--}}
@include('rapidez::components.slideover.index', ['attributes' => $attributes->class('lg:contents [&>.slideover-wrapper]:lg:contents [&>.slideover-header]:lg:hidden')])
