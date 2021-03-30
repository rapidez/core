<div class="col-span-12 sm:col-span-6">
    <x-rapidez::input
        label="Firstname"
        name="{{ $type }}_firstname"
        :placeholder="__('Firstname')"
        v-model="checkout.{{ $type }}_address.firstname"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6">
    <x-rapidez::input
        name="{{ $type }}_lastname"
        label="Lastname"
        :placeholder="__('Lastname')"
        v-model="checkout.{{ $type }}_address.lastname"
        required
    />
</div>
<div class="col-span-6 sm:col-span-3">
    <x-rapidez::input
        name="{{ $type }}_postcode"
        label="Postcode"
        :placeholder="__('Postcode')"
        v-model="checkout.{{ $type }}_address.postcode"
        required
    />
</div>
<div class="col-span-6 sm:col-span-3">
    <x-rapidez::input
        name="{{ $type }}_housenumber"
        label="Housenumber"
        :placeholder="__('Nr.')"
        v-model="checkout.{{ $type }}_address.street[1]"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::input
        name="{{ $type }}_street"
        label="Street"
        :placeholder="__('Street')"
        v-model="checkout.{{ $type }}_address.street[0]"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::input
        name="{{ $type }}_city"
        label="City"
        :placeholder="__('City')"
        v-model="checkout.{{ $type }}_address.city"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6">
    <x-rapidez::country-select
        name="{{ $type }}_country"
        label="Country"
        vModel="checkout.{{ $type }}_address.country_id"
        required
    />
</div>
<div class="col-span-12 sm:col-span-6 sm:col-start-1">
    <x-rapidez::input
        name="{{ $type }}_telephone"
        label="Telephone"
        :placeholder="__('Telephone')"
        v-model="checkout.{{ $type }}_address.telephone"
        required
    />
</div>
