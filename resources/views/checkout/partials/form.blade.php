<div class="col-span-12" v-if="$root.user">
    <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
        <div v-if="data" slot-scope="{ data }">
            <x-rapidez::select v-model="checkout.{{ $type }}_address.customer_address_id" label="">
                <option v-for="address in data.customer.addresses" :value="address.id">
                    @{{ address.firstname }} @{{ address.lastname }}
                    - @{{ address.street[0] }} @{{ address.street[1] }} @{{ address.street[2] }}
                    - @{{ address.postcode }}
                    - @{{ address.city }}
                    - @{{ address.country_code }}
                </option>
                <option :value="null">@lang('New address')</option>
            </x-rapidez::select>
        </div>
    </graphql>
</div>

<div class="contents" v-if="!$root.user || !checkout.{{ $type }}_address.customer_address_id">
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 1) ? 'sm:col-span-4' : 'sm:col-span-6'}}">
        <x-rapidez::input
            label="Firstname"
            name="{{ $type }}_firstname"
            :placeholder="__('Firstname')"
            v-model.lazy="checkout.{{ $type }}_address.firstname"
            required
        />
    </div>
    @if(Rapidez::config('customer/address/middlename_show', 1))
        <div class="col-span-12 sm:col-span-4">
            <x-rapidez::input
                name="{{ $type }}_middlename"
                label="Middlename"
                :placeholder="__('Middlename')"
                v-model.lazy="checkout.{{ $type }}_address.middlename"
            />
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 1) ? 'sm:col-span-4' : 'sm:col-span-6'}}">
        <x-rapidez::input
            name="{{ $type }}_lastname"
            label="Lastname"
            :placeholder="__('Lastname')"
            v-model.lazy="checkout.{{ $type }}_address.lastname"
            required
        />
    </div>
    <div class="col-span-6 sm:col-span-3">
        <x-rapidez::input
            name="{{ $type }}_postcode"
            label="Postcode"
            :placeholder="__('Postcode')"
            v-model.lazy="checkout.{{ $type }}_address.postcode"
            required
        />
    </div>
    @if(Rapidez::config('customer/address/street_lines', 3) >= 2)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_housenumber"
                label="Housenumber"
                :placeholder="__('Nr.')"
                v-model.lazy="checkout.{{ $type }}_address.street[1]"
                required
            />
        </div>
    @endif
    @if(Rapidez::config('customer/address/street_lines', 3) >= 3)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_addition"
                label="Addition"
                :placeholder="__('Nr.')"
                v-model.lazy="checkout.{{ $type }}_address.street[2]"
            />
        </div>
    @endif
    <div class="col-span-12 sm:col-span-6 sm:col-start-1">
        <x-rapidez::input
            name="{{ $type }}_street"
            label="Street"
            :placeholder="__('Street')"
            v-model.lazy="checkout.{{ $type }}_address.street[0]"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6 sm:col-start-1">
        <x-rapidez::input
            name="{{ $type }}_city"
            label="City"
            :placeholder="__('City')"
            v-model.lazy="checkout.{{ $type }}_address.city"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6">
        <x-rapidez::country-select
            name="{{ $type }}_country"
            label="Country"
            v-model="checkout.{{ $type }}_address.country_id"
            required
        />
    </div>
    @if(Rapidez::config('customer/address/telephone_show', 1))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input
                name="{{ $type }}_telephone"
                label="Telephone"
                :placeholder="__('Telephone')"
                v-model.lazy="checkout.{{ $type }}_address.telephone"
                :required="Rapidez::config('customer/address/telephone_show', 1) == 'req'"
            />
        </div>
    @endif
    @if(Rapidez::config('customer/address/company_show', 1))
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input
                name="{{ $type }}_company"
                label="Company"
                placeholder=""
                v-model.lazy="checkout.{{ $type }}_address.company"
                :required="Rapidez::config('customer/address/company_show', 1) == 'req'"
            />
        </div>
    @endif
</div>
