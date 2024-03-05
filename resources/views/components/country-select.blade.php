<graphql query="{ countries { two_letter_abbreviation full_name_locale } }" cache="countries">
    <div v-if="data" slot-scope="{ data }">
        <x-rapidez::input-field.select {{ $attributes }}>
            <x-slot:input>
                <option
                    v-for="country in data.countries.toSorted((a, b) =>
                        (a.full_name_locale ?? '').localeCompare(b.full_name_locale ?? '')
                    )"
                    :value="country.two_letter_abbreviation.toUpperCase()"
                >
                    @{{ country.full_name_locale }}
                </option>
            </x-slot:input>
        </x-rapidez::input-field.select>
    </div>
</graphql>
