<div class="col-span-12" v-if="$root.loggedIn">
    <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
        <div v-if="data" slot-scope="{ data }">
            <x-rapidez::select v-model="variables.customer_address_id" label="">
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

<div class="contents" v-if="!$root.loggedIn || !variables.customer_address_id">
    @if (Rapidez::config('customer/address/prefix_show', '') && strlen(Rapidez::config('customer/address/prefix_options', '')))
        <div class="col-span-12">
            <x-rapidez::select
                name="{{ $type }}_prefix"
                label="Prefix"
                v-model="variables.prefix"
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
            v-model="variables.firstname"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/middlename_show', 0))
        <div class="col-span-12 sm:col-span-4">
            <x-rapidez::input
                name="{{ $type }}_middlename"
                label="Middlename"
                v-model="variables.middlename"
            />
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
        <x-rapidez::input
            name="{{ $type }}_lastname"
            label="Lastname"
            v-model="variables.lastname"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/suffix_show', '') && strlen(Rapidez::config('customer/address/suffix_options', '')))
        <div class="col-span-12">
            <x-rapidez::select
                name="{{ $type }}_suffix"
                label="Suffix"
                v-model="variables.suffix"
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
            v-model="variables.postcode"
            v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', variables))"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/street_lines', 3) >= 2)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_housenumber"
                label="Housenumber"
                v-model="variables.street[1]"
                v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', variables))"
                required
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/street_lines', 3) >= 3)
        <div class="col-span-6 sm:col-span-3">
            <x-rapidez::input
                name="{{ $type }}_addition"
                label="Addition"
                v-model="variables.street[2]"
            />
        </div>
    @endif
    <div class="col-span-6 sm:col-span-3">
        <x-rapidez::input
            name="{{ $type }}_street"
            label="Street"
            v-model="variables.street[0]"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6 sm:col-start-1">
        <x-rapidez::input
            name="{{ $type }}_city"
            label="City"
            v-model="variables.city"
            required
        />
    </div>
    <div class="col-span-12 sm:col-span-6">
        <x-rapidez::country-select
            name="{{ $type }}_country"
            dusk="{{ $type }}_country"
            label="Country"
            v-model="variables.country_code"
            v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', variables))"
            required
        />
    </div>
    @if (Rapidez::config('customer/address/telephone_show', 'req'))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <x-rapidez::input
                name="{{ $type }}_telephone"
                label="Telephone"
                v-model="variables.telephone"
                :required="Rapidez::config('customer/address/telephone_show', 'req') == 'req'"
            />
        </div>
    @endif
    @if (Rapidez::config('customer/address/fax_show', false))
        <div class="col-span-12 sm:col-span-6">
            <x-rapidez::input
                name="{{ $type }}_fax"
                label="Fax"
                v-model="variables.fax"
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
                v-model="variables.company"
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
                v-model="variables.vat_id"
                :required="Rapidez::config('customer/address/taxvat_show', 'opt') == 'req'"
            />
        </div>
    @endif
</div>
