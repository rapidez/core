<div class="grid grid-cols-12 gap-3">
    <div class="col-span-12" v-if="$root.loggedIn">
        <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
            <div v-if="data" slot-scope="{ data }">
                <x-rapidez::input.select v-model="variables.customer_address_id">
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

    <div class="contents" v-if="!$root.loggedIn || !variables.customer_address_id">
        @if (Rapidez::config('customer/address/prefix_show', '') && strlen(Rapidez::config('customer/address/prefix_options', '')))
            <div class="col-span-12">
                <label>
                    <x-rapidez::label>@lang('Prefix')</x-rapidez::label>
                    <x-rapidez::input.select
                        name="{{ $type }}_prefix"
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
                    </x-rapidez::input.select>
                </label>
            </div>
        @endif
        <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
            <label>
                <x-rapidez::label>@lang('Firstname')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_firstname"
                    v-model="variables.firstname"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/middlename_show', 0))
            <div class="col-span-12 sm:col-span-4">
                <label>
                    <x-rapidez::label>@lang('Middlename')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_middlename"
                        v-model="variables.middlename"
                    />
                </label>
            </div>
        @endif
        <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show', 0) ? 'sm:col-span-4' : 'sm:col-span-6' }}">
            <label>
                <x-rapidez::label>@lang('Lastname')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_lastname"
                    v-model="variables.lastname"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/suffix_show', '') && strlen(Rapidez::config('customer/address/suffix_options', '')))
            <div class="col-span-12">
                <label>
                    <x-rapidez::label>@lang('Suffix')</x-rapidez::label>
                    <x-rapidez::input.select
                        name="{{ $type }}_suffix"
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
                    </x-rapidez::input.select>
                </label>
            </div>
        @endif
        <div class="col-span-6 sm:col-span-3">
            <label>
                <x-rapidez::label>@lang('Postcode')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_postcode"
                    v-model="variables.postcode"
                    v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', variables))"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/street_lines', 2) >= 2)
            <div class="col-span-6 sm:col-span-3">
                <label>
                    <x-rapidez::label>@lang('Housenumber')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_housenumber"
                        v-model="variables.street[1]"
                        v-on:change="$root.$nextTick(() => window.app.$emit('postcode-change', variables))"
                        required
                    />
                </label>
            </div>
        @endif
        @if (Rapidez::config('customer/address/street_lines', 2) >= 3)
            <div class="col-span-6 sm:col-span-3">
                <label>
                    <x-rapidez::label>@lang('Addition')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_addition"
                        v-model="variables.street[2]"
                    />
                </label>
            </div>
        @endif
        <div class="col-span-6 sm:col-span-3">
            <label>
                <x-rapidez::label>@lang('Street')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_street"
                    v-model="variables.street[0]"
                    required
                />
            </label>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <label>
                <x-rapidez::label>@lang('City')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_city"
                    v-model="variables.city"
                    required
                />
            </label>
        </div>
        <div class="col-span-12 sm:col-span-6 sm:col-start-1">
            <label>
                <x-rapidez::label>@lang('Country')</x-rapidez::label>
                <x-rapidez::input.select.country
                    name="{{ $type }}_country"
                    dusk="{{ $type }}_country"
                    v-model="variables.country_code"
                    v-on:change="$root.$nextTick(() => {
                        window.app.$emit('postcode-change', variables);
                        variables.region_id = null
                    })"
                    required
                />
            </label>
        </div>
        <div class="col-span-12 sm:col-span-6 has-[.exists]:block hidden">
            <label>
                <x-rapidez::label>@lang('Region')</x-rapidez::label>
                <x-rapidez::input.select.region
                    class="exists"
                    name="{{ $type }}_region"
                    dusk="{{ $type }}_region"
                    country="variables.country_code"
                    v-model="variables.region_id"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/telephone_show', 'req'))
            <div class="col-span-12 sm:col-span-6 sm:col-start-1">
                <label>
                    <x-rapidez::label>@lang('Telephone')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_telephone"
                        v-model="variables.telephone"
                        :required="Rapidez::config('customer/address/telephone_show', 'req') == 'req'"
                    />
                </label>
            </div>
        @endif
        @if (Rapidez::config('customer/address/fax_show', false))
            <div class="col-span-12 sm:col-span-6">
                <label>
                    <x-rapidez::label>@lang('Fax')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_fax"
                        v-model="variables.fax"
                        :required="Rapidez::config('customer/address/fax_show', false) === 'req'"
                    />
                </label>
            </div>
        @endif
        @if (Rapidez::config('customer/address/company_show', 'opt'))
            <div class="col-span-12 sm:col-span-6 sm:col-start-1">
                <label>
                    <x-rapidez::label>@lang('Company')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_company"
                        v-model="variables.company"
                        :required="Rapidez::config('customer/address/company_show', 'opt') == 'req'"
                    />
                </label>
            </div>
        @endif
        @if (Rapidez::config('customer/address/taxvat_show', 0))
            <div class="col-span-12 sm:col-span-6">
                <label>
                    <x-rapidez::label>@lang('Tax ID')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_vat_id"
                        v-model="variables.vat_id"
                        v-on:change="window.app.$emit('vat-change', $event)"
                        :required="Rapidez::config('customer/address/taxvat_show', 'opt') == 'req'"
                    />
                </label>
            </div>
        @endif
    </div>
</div>
