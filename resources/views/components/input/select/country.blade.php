<graphql query="{ countries { two_letter_abbreviation full_name_locale } }" cache="countries">
    <div v-if="data" slot-scope="{ data }">
        <x-rapidez::input.select {{ $attributes }}>
            <option v-for="country in data.countries.sort((a, b) => a.full_name_locale.localeCompare(b.full_name_locale))" :value="country.two_letter_abbreviation.toUpperCase()">
                @{{ country.full_name_locale }}
            </option>
        </x-rapidez::input.select>
    </div>
</graphql>
