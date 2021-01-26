<div class="col-span-12 sm:col-span-6">
    <x-rapidez::label for="{{ $type }}_firstname">@lang('Firstname')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_firstname"
        :placeholder="__('Firstname')"
        v-model="checkout.{{ $type }}_address.firstname"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6">
    <x-rapidez::label for="{{ $type }}_lastname">@lang('Lastname')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_lastname"
        :placeholder="__('Lastname')"
        v-model="checkout.{{ $type }}_address.lastname"
        required
    />
</div>
<div class="col-span-6 sm:col-span-3">
    <x-rapidez::label for="{{ $type }}_zipcode">@lang('Postcode')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_zipcode"
        :placeholder="__('Postcode')"
        v-model="checkout.{{ $type }}_address.zipcode"
        required
    />
</div>
<div class="col-span-6 sm:col-span-3">
    <x-rapidez::label for="{{ $type }}_housenumber">@lang('Housenumber')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_housenumber"
        :placeholder="__('Nr.')"
        v-model="checkout.{{ $type }}_address.housenumber"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::label for="{{ $type }}_street">@lang('Street')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_street"
        :placeholder="__('Street')"
        v-model="checkout.{{ $type }}_address.street"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::label for="{{ $type }}_city">@lang('City')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_city"
        :placeholder="__('City')"
        v-model="checkout.{{ $type }}_address.city"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::label for="{{ $type }}_telephone">@lang('Telephone')</x-rapidez::label>
    <x-rapidez::input
        name="{{ $type }}_telephone"
        :placeholder="__('Telephone')"
        v-model="checkout.{{ $type }}_address.telephone"
        required
    />
</div>
