<div class="grid grid-cols-12 gap-5">
    <div class="col-span-12" v-if="$root.loggedIn">
        <graphql query="{ customer { addresses { id firstname lastname street city postcode country_code } } }">
            <div v-if="data" slot-scope="{ data }">
                <x-rapidez::input.select v-model="variables.customer_address_id" dusk="{{ $type }}_address_select">
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
        @if ((Rapidez::config('customer/address/company_show')) || (Rapidez::config('customer/address/taxvat_show')))
            <div class="col-span-full">
                <div class="font-bold mb-2">@lang('Order type')</div>
                <x-rapidez::input.radio.base id="private-{{ $type }}" type="radio" name="order-type-{{ $type }}" class="peer/private hidden" v-bind:checked="!variables.company" />
                <x-rapidez::button.toggle for="private-{{ $type }}" class="peer-checked/private:ring-1 peer-checked/private:ring-primary peer-checked/private:bg-primary/10 peer-checked/private:border-primary">
                    <x-rapidez::label class="mb-0 inline">
                        @lang('Private')
                    </x-rapidez::label>
                </x-rapidez::button.toggle>

                <x-rapidez::input.radio.base id="business-{{ $type }}" type="radio" name="order-type-{{ $type }}" class="peer/business hidden" v-bind:checked="variables.company" />
                <x-rapidez::button.toggle for="business-{{ $type }}" class="peer-checked/business:ring-1 peer-checked/business:ring-primary peer-checked/business:bg-primary/10 peer-checked/business:border-primary">
                    <x-rapidez::label class="mb-0 inline">
                        @lang('Business')
                    </x-rapidez::label>
                </x-rapidez::button.toggle>

                <div class="grid col-span-12 grid-cols-12 gap-5 mt-3 transition-all duration-300 ease-in-out overflow-hidden opacity-100 h-auto peer-checked/private:opacity-0 peer-checked/private:h-0 peer-checked/private:invisible">
                    @if (Rapidez::config('customer/address/company_show'))
                        <div class="col-span-12 sm:col-span-6">
                            <label>
                                <x-rapidez::label>@lang('Company')</x-rapidez::label>
                                <x-rapidez::input
                                    name="{{ $type }}_company"
                                    v-model="variables.company"
                                    :required="Rapidez::config('customer/address/company_show') == 'req'"
                                />
                            </label>
                        </div>
                    @endif
                    @if (Rapidez::config('customer/address/taxvat_show'))
                        <div class="col-span-12 sm:col-span-6">
                            <label>
                                <x-rapidez::label>@lang('Tax ID')</x-rapidez::label>
                                <x-rapidez::input
                                    name="{{ $type }}_vat_id"
                                    v-model="variables.vat_id"
                                    v-on:change="window.app.$emit('vat-change', $event)"
                                    :required="Rapidez::config('customer/address/taxvat_show') == 'req'"
                                />
                            </label>
                        </div>
                    @endif
                </div>
            </div>
        @endif
        @if (Rapidez::config('customer/address/prefix_show') && strlen(Rapidez::config('customer/address/prefix_options')))
            <div class="col-span-12">
                <label>
                    <x-rapidez::label>@lang('Prefix')</x-rapidez::label>
                    <x-rapidez::input.select
                        name="{{ $type }}_prefix"
                        v-model="variables.prefix"
                        :required="Rapidez::config('customer/address/prefix_show') == 'req'"
                    >
                        @if (Rapidez::config('customer/address/prefix_show') === 'opt')
                            <option value=""></option>
                        @endif
                        @foreach (explode(';', Rapidez::config('customer/address/prefix_options')) as $prefix)
                            <option value="{{ $prefix }}">
                                @lang($prefix)
                            </option>
                        @endforeach
                    </x-rapidez::input.select>
                </label>
            </div>
        @endif
        <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show') ? 'sm:col-span-4' : 'sm:col-span-6' }}">
            <label>
                <x-rapidez::label>@lang('Firstname')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_firstname"
                    v-model="variables.firstname"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/middlename_show'))
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
        <div class="col-span-12 {{ Rapidez::config('customer/address/middlename_show') ? 'sm:col-span-4' : 'sm:col-span-6' }}">
            <label>
                <x-rapidez::label>@lang('Lastname')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_lastname"
                    v-model="variables.lastname"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/suffix_show') && strlen(Rapidez::config('customer/address/suffix_options')))
            <div class="col-span-12">
                <label>
                    <x-rapidez::label>@lang('Suffix')</x-rapidez::label>
                    <x-rapidez::input.select
                        name="{{ $type }}_suffix"
                        v-model="variables.suffix"
                        :required="Rapidez::config('customer/address/suffix_show') == 'req'"
                    >
                        @if (Rapidez::config('customer/address/suffix_show') === 'opt')
                            <option value=""></option>
                        @endif
                        @foreach (explode(';', Rapidez::config('customer/address/suffix_options')) as $suffix)
                            <option value="{{ $suffix }}">
                                @lang($suffix)
                            </option>
                        @endforeach
                    </x-rapidez::input.select>
                </label>
            </div>
        @endif
        <div class="col-span-12 sm:has-[+*_.region.exists]:col-span-3 sm:col-span-6">
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
        <div class="col-span-12 sm:col-span-3 has-[.region.exists]:block hidden">
            <label>
                <x-rapidez::label>@lang('Region')</x-rapidez::label>
                <x-rapidez::input.select.region
                    class="region exists"
                    name="{{ $type }}_region"
                    dusk="{{ $type }}_region"
                    country="variables.country_code"
                    v-model="variables.region_id"
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/telephone_show'))
            <div class="col-span-12 sm:col-span-6">
                <label>
                    <x-rapidez::label>@lang('Telephone')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_telephone"
                        v-model="variables.telephone"
                        :required="Rapidez::config('customer/address/telephone_show') == 'req'"
                    />
                </label>
            </div>
        @endif
        <div class="col-span-12 {{ Rapidez::config('customer/address/street_lines') >= 3 ? 'sm:col-span-4' : 'sm:col-span-6' }}">
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
        @if (Rapidez::config('customer/address/street_lines') >= 2)
            <div class="{{ Rapidez::config('customer/address/street_lines') >= 3 ? 'col-span-6 sm:col-span-4' : 'col-span-12 sm:col-span-6' }}">
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
        @if (Rapidez::config('customer/address/street_lines') >= 3)
            <div class="col-span-6 sm:col-span-4">
                <label>
                    <x-rapidez::label>@lang('Addition')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_addition"
                        v-model="variables.street[2]"
                    />
                </label>
            </div>
        @endif
        <div class="col-span-12 sm:col-span-6">
            <label>
                <x-rapidez::label>@lang('Street')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_street"
                    v-model="variables.street[0]"
                    required
                />
            </label>
        </div>
        <div class="col-span-12 sm:col-span-6">
            <label>
                <x-rapidez::label>@lang('City')</x-rapidez::label>
                <x-rapidez::input
                    name="{{ $type }}_city"
                    v-model="variables.city"
                    required
                />
            </label>
        </div>
        @if (Rapidez::config('customer/address/fax_show'))
            <div class="col-span-12 sm:col-span-6">
                <label>
                    <x-rapidez::label>@lang('Fax')</x-rapidez::label>
                    <x-rapidez::input
                        name="{{ $type }}_fax"
                        v-model="variables.fax"
                        :required="Rapidez::config('customer/address/fax_show') === 'req'"
                    />
                </label>
            </div>
        @endif
    </div>
</div>
