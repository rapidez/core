@props(['location'])

@if(Rapidez::config('recaptcha_frontend/type_for/'.$location) == 'recaptcha_v3' && $key = Rapidez::config('recaptcha_frontend/type_recaptcha_v3/public_key', null, true))
@push('head')
<script src="https://www.google.com/recaptcha/api.js?render={{ $key }}" async defer></script>
@endpush
@endif
