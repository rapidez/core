<graphql query="{ countries { two_letter_abbreviation full_name_locale } }" cache="countries">
    <x-rapidez::input.select :$attributes v-if="data" slot-scope="{ data }">
        <option v-for="country in data.countries" :value="country.two_letter_abbreviation.toUpperCase()">
            @{{ country.full_name_locale }}
        </option>
    </x-rapidez::input.select>
</graphql>
