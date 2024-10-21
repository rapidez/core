<div class="col-span-12" v-if="$root.loggedIn">
    <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
        <div v-if="addressQueryScope.data" slot-scope="addressQueryScope">
            <x-rapidez::select v-model="checkoutScope.checkout.{{ $type }}_address.customer_address_id" label="">
                <option v-for="address in addressQueryScope.data.customer.addresses" :value="address.id">
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

<div class="contents" v-if="!$root.loggedIn || !checkoutScope.checkout.{{ $type }}_address.customer_address_id">
    @if (Rapidez::config('customer/address/prefix_show', '') && strlen(Rapidez::config('customer/address/prefix_options', '')))
        <div class="col-span-12">
            <x-rapidez::select
                name="{{ $type }}_prefix"
                label="Prefix"
                v-model="checkoutScope.checkout.{{ $type }}_address.prefix"
                :required="Rapidez::config('customer/address/prefix_show', 'opt') == 'req'"
            >
                @if (Rapidez::config('customer/address/prefix_show', '') === 'opt')
                    <option value=""></option>
                @endif
                @foreach (explode(';', Rapidez::config('customer/address/prefix_options', '')) as $prefix)
                    <option value="{{ $prefix }}">
                        @lang($prefix)
                    </option>
                @endforeach
            </x-rapidez::select>
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
        <x-rapidez::input
            label="Firstname"
            name="{{ $type }}_firstname"
            v-model.lazy="checkoutScope.checkout.{{ $type }}_address.firstname"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/middlename_show', 0))
        <div class="col-span-12 sm:col-span-4">
            <x-rapidez::input
                name="{{ $type }}_middlename"
                label="Middlename"
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.middlename"
            />
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
        <x-rapidez::input
            name="{{ $type }}_lastname"
            label="Lastname"
            v-model.lazy="checkoutScope.checkout.{{ $type }}_address.lastname"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/suffix_show', '') && strlen(Rapidez::config('customer/address/suffix_options', '')))
        <div class="col-span-12">
            <x-rapidez::select
                name="{{ $type }}_suffix"
                label="Suffix"
                v-model="checkoutScope.checkout.{{ $type }}_address.suffix"
                :required="Rapidez::config('customer/address/suffix_show', 'opt') == 'req'"
            >
                @if (Rapidez::config('customer/address/suffix_show', '') === 'opt')
                    <option value=""></option>
                @endif
                @foreach (explode(';', Rapidez::config('customer/address/suffix_options', '')) as $suffix)
                    <option value="{{ $suffix }}">
                        @lang($suffix)
                    </option>
                @endforeach
            </x-rapidez::select>
        </div>
    @endif
    <div class="col-span-6 sm:col-span-3">
        <x-rapidez::input
            name="{{ $type }}_postcode"
            label="Postcode"
            v-model.lazy="checkoutScope.checkout.{{ $type }}_address.postcode"
            v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkoutScope.checkout.{{ $type }}_address))"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/street_lines', 3) >= 2)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_housenumber"
                label="Housenumber"
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.street[1]"
                v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkoutScope.checkout.{{ $type }}_address))"
                required
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/street_lines', 3) >= 3)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_addition"
                label="Addition"
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.street[2]"
            />
        </div>
    @endif
    <div class="col-span-6 sm:col-span-3">
        <x-rapidez::input
            name="{{ $type }}_street"
            label="Street"
            v-model.lazy="checkoutScope.checkout.{{ $type }}_address.street[0]"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6 sm:col-start-1">
        <x-rapidez::input
            name="{{ $type }}_city"
            label="City"
            v-model.lazy="checkoutScope.checkout.{{ $type }}_address.city"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6">
        <x-rapidez::country-select
            name="{{ $type }}_country"
            dusk="{{ $type }}_country"
            label="Country"
            v-model="checkoutScope.checkout.{{ $type }}_address.country_id"
            v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkoutScope.checkout.{{ $type }}_address))"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/telephone_show', 'req'))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input
                name="{{ $type }}_telephone"
                label="Telephone"
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.telephone"
                :required="Rapidez::config('customer/address/telephone_show', 'req') == 'req'"
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/fax_show', false))
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input
                name="{{ $type }}_fax"
                label="Fax"
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.fax"
                :required="Rapidez::config('customer/address/fax_show', false) === 'req'"
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/company_show', 'opt'))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input
                name="{{ $type }}_company"
                label="Company"
                placeholder=""
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.company"
                :required="Rapidez::config('customer/address/company_show', 'opt') == 'req'"
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/taxvat_show', 0))
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input
                name="{{ $type }}_vat_id"
                label="Tax ID"
                placeholder=""
                v-model.lazy="checkoutScope.checkout.{{ $type }}_address.vat_id"
                :required="Rapidez::config('customer/address/taxvat_show', 'opt') == 'req'"
            />
        </div>
    @endif
</div>
