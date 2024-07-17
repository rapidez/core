<div class="col-span-12" v-if="$root.loggedIn">
    <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
        <div v-if="data" slot-scope="{ data }">
            <x-rapidez::input.select v-model="checkout.{{ $type }}_address.customer_address_id">
                <option v-for="address in data.customer.addresses" :value="address.id">
                    @{{ address.firstname }} @{{ address.lastname }}
                    - @{{ address.street[0] }} @{{ address.street[1] }} @{{ address.street[2] }}
                    - @{{ address.postcode }}
                    - @{{ address.city }}
                    - @{{ address.country_code }}
                </option>
                <option :value="null">@lang('New address')</option>
            </x-rapidez::input.select>
        </div>
    </graphql>
</div>

<div class="contents" v-if="!$root.loggedIn || !checkout.{{ $type }}_address.customer_address_id">
    @if (Rapidez::config('customer/address/prefix_show', '') && strlen(Rapidez::config('customer/address/prefix_options', '')))
        <div class="col-span-12">
            <label>
                <x-rapidez::input.label>@lang('Prefix')</x-rapidez::input.label>
                <x-rapidez::input.select
                    name="{{ $type }}_prefix"
                    v-model="checkout.{{ $type }}_address.prefix"
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
                </x-rapidez::input.select>
            </label>
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
        <label>
            <x-rapidez::input.label>@lang('Firstname')</x-rapidez::input.label>
            <x-rapidez::input
                name="{{ $type }}_firstname"
                v-model.lazy="checkout.{{ $type }}_address.firstname"
                required
            />
        </label>
    </div>
    @if (Rapidez::config('customer/address/middlename_show', 0))
        <div class="col-span-12 sm:col-span-4">
            <label>
                <x-rapidez::input.label>@lang('Middlename')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_middlename"
                    v-model.lazy="checkout.{{ $type }}_address.middlename"
                />
            </label>
        </div>
    @endif
    <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
        <label>
            <x-rapidez::input.label>@lang('Lastname')</x-rapidez::input.label>
            <x-rapidez::input
                name="{{ $type }}_lastname"
                v-model.lazy="checkout.{{ $type }}_address.lastname"
                required
            />
        </label>
    </div>
    @if (Rapidez::config('customer/address/suffix_show', '') && strlen(Rapidez::config('customer/address/suffix_options', '')))
        <div class="col-span-12">
            <label>
                <x-rapidez::input.label>@lang('Suffix')</x-rapidez::input.label>
                <x-rapidez::input.select
                    name="{{ $type }}_suffix"
                    v-model="checkout.{{ $type }}_address.suffix"
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
                </x-rapidez::input.select>
            </label>
        </div>
    @endif
    <div class="col-span-6 sm:col-span-3">
        <label>
            <x-rapidez::input.label>@lang('Postcode')</x-rapidez::input.label>
            <x-rapidez::input
                name="{{ $type }}_postcode"
                v-model.lazy="checkout.{{ $type }}_address.postcode"
                v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkout.{{ $type }}_address))"
                required
            />
        </label>
    </div>
    @if (Rapidez::config('customer/address/street_lines', 3) >= 2)
        <div class="col-span-6 sm:col-span-3">
            <label>
                <x-rapidez::input.label>@lang('Housenumber')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_housenumber"
                    type="number"
                    v-model.lazy="checkout.{{ $type }}_address.street[1]"
                    v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkout.{{ $type }}_address))"
                    required
                />
            </label>
        </div>
    @endif
    @if (Rapidez::config('customer/address/street_lines', 3) >= 3)
        <div class="col-span-6 sm:col-span-3">
            <label>
                <x-rapidez::input.label>@lang('Addition')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_addition"
                    v-model.lazy="checkout.{{ $type }}_address.street[2]"
                />
            </label>
        </div>
    @endif
    <div class="col-span-6 sm:col-span-3">
        <label>
            <x-rapidez::input.label>@lang('Street')</x-rapidez::input.label>
            <x-rapidez::input
                name="{{ $type }}_street"
                v-model.lazy="checkout.{{ $type }}_address.street[0]"
                required
            />
        </label>
    </div>
    <div class="col-span-12 sm:col-span-6 sm:col-start-1">
        <label>
            <x-rapidez::input.label>@lang('City')</x-rapidez::input.label>
            <x-rapidez::input
                name="{{ $type }}_city"
                v-model.lazy="checkout.{{ $type }}_address.city"
                required
            />
        </label>
    </div>
    <div class="col-span-12 sm:col-span-6">
        <label>
            <x-rapidez::input.label>@lang('Country')</x-rapidez::input.label>
            <x-rapidez::country-select
                name="{{ $type }}_country"
                dusk="{{ $type }}_country"
                v-model="checkout.{{ $type }}_address.country_id"
                v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', checkout.{{ $type }}_address))"
                required
            />
        </label>
    </div>
    @if (Rapidez::config('customer/address/telephone_show', 'req'))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <label>
                <x-rapidez::input.label>@lang('Telephone')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_telephone"
                    type="tel"
                    v-model.lazy="checkout.{{ $type }}_address.telephone"
                    :required="Rapidez::config('customer/address/telephone_show', 'req') == 'req'"
                />
            </label>
        </div>
    @endif
    @if (Rapidez::config('customer/address/fax_show', false))
        <div class="col-span-12 sm:col-span-6">
            <label>
                <x-rapidez::input.label>@lang('Fax')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_fax"
                    v-model.lazy="checkout.{{ $type }}_address.fax"
                    :required="Rapidez::config('customer/address/fax_show', false) === 'req'"
                />
            </label>
        </div>
    @endif
    @if (Rapidez::config('customer/address/company_show', 'opt'))
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <label>
                <x-rapidez::input.label>@lang('Company')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_company"
                    v-model.lazy="checkout.{{ $type }}_address.company"
                    :required="Rapidez::config('customer/address/company_show', 'opt') == 'req'"
                />
            </label>
        </div>
    @endif
    @if (Rapidez::config('customer/address/taxvat_show', 0))
        <div class="col-span-12 sm:col-span-6">
            <label>
                <x-rapidez::input.label>@lang('Tax ID')</x-rapidez::input.label>
                <x-rapidez::input
                    name="{{ $type }}_vat_id"
                    v-model.lazy="checkout.{{ $type }}_address.vat_id"
                    :required="Rapidez::config('customer/address/taxvat_show', 'opt') == 'req'"
                />
            </label>
        </div>
    @endif
</div>
